class ApiService {
    constructor(baseURL = '') {
      this.baseURL = baseURL;
      this.defaultHeaders = {
        'Content-Type': 'application/json',
      };
    }
  
    getFullURL(endpoint) {
      return `${this.baseURL}${endpoint}`;
    }
  
    /**
     * Универсальный метод request, который:
     * 1. Делает fetch
     * 2. Считывает response.body как текст
     * 3. Опционально парсит JSON (если заголовок соответствующий)
     */
    async request(method, endpoint, body = null, headers = {}) {
      const url = this.getFullURL(endpoint);
  
      // Проверим, не FormData ли мы посылаем
      const isFormData = body instanceof FormData;
  
      // Прочитаем CSRF-токен из localStorage (если используется)
      const csrfToken = localStorage.getItem('csrf_token');
      if (csrfToken) {
        headers['X-CSRF-Token'] = csrfToken;
      }
  
      // Склеим заголовки: если FormData, убираем 'Content-Type', иначе JSON
      headers = {
        ...(isFormData ? {} : { 'Content-Type': 'application/json' }),
        ...headers,
      };
  
      const options = {
        method: method.toUpperCase(),
        headers,
        credentials: 'include', // Для включения кук/сессии
      };
  
      if (body) {
        options.body = isFormData ? body : JSON.stringify(body);
      }
  
      try {
        const response = await fetch(url, options);
        const contentType = response.headers.get('content-type') || '';
        const rawResponseText = await response.text();
  
        // Если пришел статус не 2xx, выбрасываем ошибку с сырым текстом
        if (!response.ok) {
          throw new Error(
            `Error ${response.status}: ${rawResponseText || 'No response body'}`
          );
        }
  
        // Проверяем, действительно ли ответ JSON
        if (contentType.includes('application/json')) {
          // Пробуем распарсить JSON
          try {
            return JSON.parse(rawResponseText);
          } catch (parseError) {
            // Если парсинг рухнул, логируем все данные
            console.error('Ошибка парсинга JSON:', parseError);
            console.error('Тело ответа (raw):', rawResponseText);
            throw new Error(`JSON parse error: ${parseError.message}`);
          }
        } else {
          // Если это не JSON, возвращаем как есть
          return rawResponseText;
        }
      } catch (error) {
        // Логируем ошибку, метод и endpoint
        console.error(`Request ${method.toUpperCase()} ${endpoint} failed:`, error);
        // Пробрасываем дальше, чтобы catch в компоненте тоже сработал
        throw error;
      }
    }
  
    // Шорткаты под основные HTTP-методы
    get(endpoint, headers = {}) {
      return this.request('GET', endpoint, null, headers);
    }
  
    post(endpoint, body, headers = {}) {
      return this.request('POST', endpoint, body, headers);
    }
  
    put(endpoint, body, headers = {}) {
      return this.request('PUT', endpoint, body, headers);
    }
  
    delete(endpoint, body = null, headers = {}) {
      return this.request('DELETE', endpoint, body, headers);
    }
  }
  
  export default ApiService;
  
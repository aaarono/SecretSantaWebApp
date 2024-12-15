<?php

namespace Secret\Santa\Models;

use Secret\Santa\Config\Database;
use PDO;

class SmsModel
{
    private $conn;

    public function __construct()
    {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAllSmsByGameId($gameId)
    {
        $query = 'SELECT * FROM "SMS" WHERE game_id = :game_id ORDER BY created_at ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_STR);
        $stmt->execute();
        $smsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($smsList as &$sms) {
            $sms['message'] = $this->decryptMessage($sms['message_encrypted']);
            unset($sms['message_encrypted']);
        }

        return $smsList;
    }

     public function getAllSms()
    {
        $query = 'SELECT * FROM "SMS" ORDER BY created_at ASC';
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        $smsList = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($smsList as &$sms) {
            $sms['message'] = $this->decryptMessage($sms['message_encrypted']);
            unset($sms['message_encrypted']);
        }

        return $smsList;
    }

    private function decryptMessage($data)
    {
        $encryptionKey = getenv('ENCRYPTION_KEY');
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length('aes-256-cbc');
        $iv = substr($data, 0, $ivLength);
        $encryptedMessage = substr($data, $ivLength);
        return openssl_decrypt($encryptedMessage, 'aes-256-cbc', $encryptionKey, 0, $iv);
    }

    public function getSmsById($id)
    {
        $query = 'SELECT * FROM "SMS" WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }


    public function createSms($gameId, $login, $message)
    {
        $encryptedMessage = $this->encryptMessage($message);
        $query = 'INSERT INTO "SMS" (game_id, login, message_encrypted) VALUES (:game_id, :login, :message_encrypted)';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':game_id', $gameId, PDO::PARAM_STR);
        $stmt->bindParam(':login', $login, PDO::PARAM_STR);
        $stmt->bindParam(':message_encrypted', $encryptedMessage, PDO::PARAM_STR);
        return $stmt->execute();
    }

    private function encryptMessage($message)
    {
        $encryptionKey = getenv('ENCRYPTION_KEY');
        $iv = openssl_random_pseudo_bytes(openssl_cipher_iv_length('aes-256-cbc'));
        $encrypted = openssl_encrypt($message, 'aes-256-cbc', $encryptionKey, 0, $iv);
        return base64_encode($iv . $encrypted);
    }

    public function updateSms($id, $message)
    {
        $messageHash = hash('sha256', $message);
        $query = 'UPDATE "SMS" SET message_hash = :message_hash WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':message_hash', $messageHash, PDO::PARAM_STR);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    public function deleteSms($id)
    {
        $query = 'DELETE FROM "SMS" WHERE id = :id';
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

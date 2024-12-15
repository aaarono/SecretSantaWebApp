import React, { useState, useEffect, useContext } from 'react';
import { useNavigate } from 'react-router-dom';
import Logo from '../../components/Logo/Logo';
import Header from '../../components/Header/Header';
import defaultAvatar from '../../assets/avatar.png';
import '../../index.css';
import './SettingsPage.css';
import TextInput from '../../components/ui/TextInput/TextInput';
import Button from '../../components/ui/Button/Button';
import SelectInput from '../../components/ui/SelectInput/SelectInput';
import { logout } from '../../services/api/authService';
import api from '../../services/api/api';
import { AvatarContext } from '../../components/contexts/AvatarContext';
import { UserContext } from '../../components/contexts/UserContext';

const uploadUserImage = async (file) => {
    const formData = new FormData();
    formData.append('image', file);

    try {
        const response = await api.post('/user/update-image', formData, {
            headers: {
                'Content-Type': 'multipart/form-data',
            },
        });
        return response;
    } catch (error) {
        console.error('Failed to upload user image:', error);
        throw error;
    }
};

const getUserImage = async () => {
    try {
        const response = await api.get('/user/get-image');
        if (response.status === 'success' && response.image) {
            return response.image;
        } else {
            throw new Error(response.message || 'Failed to retrieve image');
        }
    } catch (error) {
        console.error('Failed to fetch user image:', error);
        throw error;
    }
};

const deleteUserImage = async () => {
    try {
        const response = await api.post('/user/delete-image');
        return response;
    } catch (error) {
        console.error('Failed to delete user image:', error);
        throw error;
    }
};

const SettingsPage = () => {
    const navigate = useNavigate();
    const { user, updateUser } = useContext(UserContext);
    const [formValues, setFormValues] = useState({
        firstName: '',
        lastName: '',
        phoneNumber: '',
        email: '',
        gender: '',
    });

    const { avatar, setAvatar } = useContext(AvatarContext);
    const [isLoading, setIsLoading] = useState(false);

    useEffect(() => {
        const fetchUserData = async () => {
            try {
                const response = await api.get('/user/get-data');
                console.log('Response from server:', response);

                if (response.status === 'success') {
                    const userData = response.data;
                    console.log('User data:', userData);

                    setFormValues({
                        firstName: userData.firstname || '',
                        lastName: userData.lastname || '',
                        phoneNumber: userData.phonenumber || '',
                        email: userData.email || '',
                        gender: userData.gender || '',
                    });

                    console.log('Form values:', {
                        firstName: userData.firstname || '',
                        lastName: userData.lastname || '',
                        phoneNumber: userData.phonenumber || '',
                        email: userData.email || '',
                        gender: userData.gender || '',
                    });
                } else {
                    console.error(response.message || 'Failed to fetch user data');
                }
            } catch (error) {
                console.error('Error fetching user data:', error);
            }
        };

        fetchUserData();
    }, []);

    const handleInputChange = (e) => {
        const { name, value } = e.target;
        setFormValues(prevValues => ({
            ...prevValues,
            [name]: value
        }));
    };

    const handleImageUpload = async (e) => {
        const file = e.target.files[0];
        if (!file) return;

        const allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
        if (!allowedTypes.includes(file.type)) {
            alert('Only JPEG, PNG, and GIF files are allowed.');
            return;
        }

        try {
            setIsLoading(true);
            const response = await uploadUserImage(file);
            if (response.status === 'success') {
                setAvatar(response.image);
            }
        } catch (error) {
            console.error('Failed to upload image:', error);
        } finally {
            setIsLoading(false);
        }
    };

    const handleImageDelete = async () => {
        try {
            setIsLoading(true);
            const response = await deleteUserImage();
            if (response.status === 'success') {
                setAvatar(defaultAvatar);
            }
        } catch (error) {
            console.error('Failed to delete image:', error);
        } finally {
            setIsLoading(false);
        }
    };

    const handleSave = async () => {
        try {
            setIsLoading(true);
            await updateUser(formValues);
        } catch (error) {
            console.error('Error updating user data:', error);
        } finally {
            setIsLoading(false);
        }
    };

    const fetchOnLogout = () => {
        logout();
        navigate('/auth');
    };

    return (
        <div className='settings-container'>
            <h2>Settings</h2>
            <div className='settings-img'>
                {isLoading ? (
                    <div className="spinner">Loading...</div>
                ) : (
                    <div className='avatar-settings-container'><img src={avatar} className='avatar-settings' alt="Avatar" /></div>
                )}
                <div className='settings-img-change'>
                    <p>Change Avatar:</p>
                    <input type='file' accept='image/jpeg, image/png, image/gif' onChange={handleImageUpload} disabled={isLoading} />
                    <Button text='Delete' type='button' onClick={handleImageDelete} disabled={isLoading} />
                </div>
            </div>
            <div className='settings-inputs'>
                <TextInput
                    name="firstName"
                    type='text'
                    placeholder="First Name"
                    value={formValues.firstName}
                    onChange={handleInputChange}
                />
                <TextInput
                    name="lastName"
                    type='text'
                    placeholder="Last Name"
                    value={formValues.lastName}
                    onChange={handleInputChange}
                />
                <TextInput
                    name="phoneNumber"
                    type='phone'
                    placeholder="Phone"
                    value={formValues.phoneNumber}
                    onChange={handleInputChange}
                />
                <TextInput
                    name="email"
                    type='email'
                    placeholder="E-mail"
                    value={formValues.email}
                    onChange={handleInputChange}
                />
                <select
                    name="gender"
                    value={formValues.gender}
                    onChange={handleInputChange}
                >
                    <option value="" disabled>
                        Gender
                    </option>
                    <option value="male">Male</option>
                    <option value="female">Female</option>
                </select>
                <Button text="Log Out" type="submit" onClick={fetchOnLogout} />
            </div>
            <div className='settings-button'>
                <Button text="Save" type="button" onClick={handleSave} disabled={isLoading} />
            </div>
        </div>
    );
};

export default SettingsPage;
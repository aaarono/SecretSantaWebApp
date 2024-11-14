import React, { useState } from 'react';
import { useNavigate } from 'react-router-dom';
import Logo from '../../components/Logo/Logo';
import Header from '../../components/Header/Header';
import defaultAvatar from '../../assets/avatar.png';
import '../../index.css';
import './SettingsPage.css';
import TextInput from '../../components/ui/TextInput/TextInput';
import Button from '../../components/ui/Button/Button';
import SelectInput from '../../components/ui/SelectInput/SelectInput';

const SettingsPage = () => {
  const navigate = useNavigate();

  const [formValues, setFormValues] = useState({
    firstName: '',
    lastName: '',
    phoneNumber: '',
    email: '',
  });

  const handleInputChange = (e) => {
    const { name, value } = e.target;
    setFormValues({ ...formValues, [name]: value });
  };

  return (
    <>
        <Logo/>
        <Header username={'VasyaPupkin228'} email={'vasyapupkin228@gmail.com'}/>
        <div className='settings-container'>
            <h2>Settings</h2>
            <div className='settings-img'>
              <img src={defaultAvatar} alt="Avatar" />
              <div className='settings-img-change'>
                <p>Change Avatar: </p>
                <Button text = 'Upload' type = 'submit' onClick={handleInputChange}/>
              </div>
            </div>
            <div className='settings-inputs'>
              <div>
                <TextInput
                name="firstName"
                type='text'
                placeholder="First Name"
                value={formValues.firstName}
                onChange={handleInputChange}
                disabled
              />
              </div>
              <div>
              <TextInput
                name="lastName"
                type='text'
                placeholder="Last Name"
                value={formValues.lastName}
                onChange={handleInputChange}
                disabled
              />
              </div>
              <div>
              <TextInput
                name="phoneNumber"
                type='phone'
                placeholder="Phone"
                value={formValues.phoneNumber}
                onChange={handleInputChange}
                disabled
              />
              </div>
              <TextInput
                name="email"
                type='email'
                placeholder="E-mail"
                value={formValues.email}
                onChange={handleInputChange}
              />
              <div>
                <SelectInput />
              </div>
              <div>
                <Button text="Log Out" type="submit"/>
              </div>
            </div>
            <div className='settings-button'>
              <Button text="Save" type="submit"/>
            </div>
        </div>
    </>
  );
};

export default SettingsPage;
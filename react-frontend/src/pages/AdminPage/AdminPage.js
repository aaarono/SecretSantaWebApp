import React, { useState, useEffect } from 'react';
import axios from 'axios';
import Button from '../../components/ui/Button/Button';
import Logo from '../../components/Logo/Logo';
import '../../index.css';
import './AdminPage.css';
import api from '../../services/api/api';

const AdminPage = () => {
    const [activeTable, setActiveTable] = useState('users');
    const [data, setData] = useState([]);
    const [modalIsOpen, setModalIsOpen] = useState(false);
    const [formData, setFormData] = useState({});
    const [isUpdate, setIsUpdate] = useState(false);

    useEffect(() => {
        fetchData(activeTable);
    }, [activeTable]);

    const fetchData = (table) => {
        api.get(`/api/${table}`)
            .then(response => {
                console.log('Fetched data:', response);
                setData(response);
            })
            .catch(error => console.error('Error fetching data:', error));
    };

    const handleDelete = (id) => {
        api.delete(`/api/${activeTable}/${id}`)
            .then(() => {
                console.log('Deleted item:', id);
                fetchData(activeTable);
            })
            .catch(error => console.error('Error deleting item:', error));
    };

    const handleSubmit = (e) => {
        e.preventDefault();
        const method = isUpdate ? 'put' : 'post';
        console.log('Submitting form data:', formData);

        api[method](`/api/${activeTable}`, formData)
            .then(() => {
                console.log('Form submitted successfully');
                fetchData(activeTable);
                setModalIsOpen(false);
                setIsUpdate(false);
            })
            .catch(error => console.error('Error submitting form:', error));
    };

    const renderTable = () => (
        <table>
            <thead>
                <tr>
                    {data.length > 0 && Object.keys(data[0]).map((key) => <th key={key}>{key}</th>)}
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                {data.map((item) => (
                    <tr key={item.id || item.uuid}>
                        {Object.values(item).map((value, index) => (
                            <td key={index}>{value}</td>
                        ))}
                        <td>
                            <button onClick={() => handleDelete(item.id || item.uuid)}>Delete</button>
                            <button onClick={() => {
                                setFormData(item);
                                setIsUpdate(true);
                                setModalIsOpen(true);
                            }}>Update</button>
                        </td>
                    </tr>
                ))}
            </tbody>
        </table>
    );

    return (
        <>
            <Logo />
            <div className="admin-container">
                <div className="section-container">
                    <div className="admin-section">
                        <h1>Admin Panel</h1>
                        <div className="admin-buttons">
                            <Button text={'Users'} onClick={() => setActiveTable('users')} />
                            <Button text={'Games'} onClick={() => setActiveTable('games')} />
                            <Button text={'Pairs'} onClick={() => setActiveTable('pairs')} />
                            <Button text={'Wishlists'} onClick={() => setActiveTable('wishlists')} />
                            <Button text={'Player_Game'} onClick={() => setActiveTable('player_game')} />
                        </div>
                    </div>
                </div>

                <div className="section-container">
                    <div className="admin-table">
                        <h1>{activeTable.charAt(0).toUpperCase() + activeTable.slice(1)}</h1>
                        <Button 
                            text={`Add ${activeTable}`} 
                            onClick={() => {
                                setFormData({});
                                setIsUpdate(false);
                                setModalIsOpen(true);
                            }} 
                        />
                        {renderTable()}
                    </div>
                </div>
            </div>

            {modalIsOpen && (
                <div className="modal">
                    <form onSubmit={handleSubmit}>
                        {Object.keys(formData || {}).map((key) => (
                            <div key={key}>
                                <label>{key}</label>
                                <input
                                    type="text"
                                    value={formData[key] || ''}
                                    onChange={(e) => setFormData({ ...formData, [key]: e.target.value })}
                                />
                            </div>
                        ))}
                        <button type="submit">Save</button>
                        <button type="button" onClick={() => setModalIsOpen(false)}>Cancel</button>
                    </form>
                </div>
            )}
        </>
    );
};

export default AdminPage;

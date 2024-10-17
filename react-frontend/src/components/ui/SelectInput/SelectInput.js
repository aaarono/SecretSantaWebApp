import React from 'react';
import '../../../index.css';
import './SelectInput.css';

const SelectInput = ({ props }) => {
    return (
        <div className="select-input-container">
            <select defaultValue={""} {...props}>
                <option value="" disabled>Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
    );
}

export default SelectInput;
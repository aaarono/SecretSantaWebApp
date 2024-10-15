import React from 'react';
import '../../../index.css';

const SelectInput = ({ props, style }) => {
    return (
        <div className="select-input-container">
            <select {...props} style={{ ...{}, ...style }}>
                <option>Gender</option>
                <option value="male">Male</option>
                <option value="female">Female</option>
            </select>
        </div>
    );
}

export default SelectInput;
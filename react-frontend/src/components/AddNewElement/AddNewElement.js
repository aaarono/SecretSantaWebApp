import React from 'react';
import '../../index.css';
import './AddNewElement.css';
import addNew from '../../assets/addNew.svg';
import { SlPlus } from "react-icons/sl";

const AddNewElement = () => {
  return (
    <div className='add-new-element-container'>
        <SlPlus />
    </div>
  );
};

export default AddNewElement;
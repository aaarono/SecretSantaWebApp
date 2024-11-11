import React from 'react';
import '../../index.css';
import './AddNewElement.css';
import addNew from '../../assets/addNew.svg';

const AddNewElement = () => {
  return (
    <div className='add-new-element-container'>
        <a href='#'><img src={addNew} alt='Add'/></a>
    </div>
  );
};

export default AddNewElement;
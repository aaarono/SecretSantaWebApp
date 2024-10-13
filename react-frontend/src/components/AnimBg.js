import React from 'react';
import '../index.css'; // Styles will be applied from index.css

const AnimBg = ({ style, children }) => {
  return (
    <div className="bg-snow" style={style}>
        <div className="bg-snow-inner">
            {children}
        </div>
    </div>
  );
};

export default AnimBg;
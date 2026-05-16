const button = () => { 
	return React.createElement('button', {style:{}}, 'hey');
};

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(button());
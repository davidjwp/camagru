let i = 0;

function log(){console.log(`clicked ${i++} times`);};

const button = () => { 
	return React.createElement('button', {style:{}, onClick: log}, 'hey');
};

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(button());
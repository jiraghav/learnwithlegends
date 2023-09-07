import React from 'react';
import ReactDOM from 'react-dom/client';
import App from './App';


function renderComponent() {
  var reactAppContainer;
  if (reactAppContainer = document.getElementById('react-app')) {
    const reactApp = ReactDOM.createRoot(reactAppContainer);
    reactApp.render(<App toAccount={reactAppContainer.getAttribute('wallet-address')}
                         dollarAmount={reactAppContainer.getAttribute('dollar-amount')}
                         confirmTransferUrl={reactAppContainer.getAttribute('confirm-transfer-url')}
                    />);
  }
}

window.initReact = renderComponent;

function unmountComponent() {
  ReactDOM.unmountComponentAtNode(document.getElementById('react-app'));
}

window.unmountReact = unmountComponent;

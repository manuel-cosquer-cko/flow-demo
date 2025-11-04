document.getElementById('reset_button').addEventListener('click', async () => {
    document.getElementById('interblock').style.display = 'none';

    const order = await fetch('/payment-session', { method: 'POST' });
    const paymentSession = await order.json();
  
    const checkoutWebComponents = await CheckoutWebComponents({
      paymentSession,
      publicKey: document.getElementById('payments').getAttribute('key'),
      environment: 'sandbox',
      locale: document.getElementById('form-local').value,
      appearance: {
        colorPrimary: "#000000", // label and input
        colorDisabled: "#c1c1c4", // placeholder
        colorError: "#d82829", // error message
        colorOutline: "transparent", // set to transparent to remove outline
        colorFormBackground: "#f2f5f8", // form background
        input: {
            lineHeight: "16px",
            fontFamily:  "Roboto, sans-serif",
            fontSize: "14px",
        }
      },
      componentOptions: {
        flow: {
          showPayButton: true
        },
        card: {
          data: {
            cardholderName: 'John Smith'
          },
            displayCardholderName: 'top'
        }
      }
    });

  
    const flow = checkoutWebComponents.create('flow', {
      /*handleClick: (_self) => {
        const element = document.getElementById('error-message');  
        element.innerHTML = `
  <div class="alert alert-warning alert-dismissible fade show d-flex align-items-center" role="alert">
    <strong class="me-2">Attention :</strong> pique assiette
  </div>`;

        return { continue: false };
      },
      onCardBinChanged: async (_self) => {
          // partie métier

        return { continue: false, errorMessage: 'Pique-assiette' };
      },
      handleSubmit: async (self, submitData) => {
        console.log(submitData);
        const submitResponse = await submitPaymentSession(submitData);

        return submitResponse;
      },*/
      onPaymentCompleted: async (_self, paymentResponse) => {
        const element = document.getElementById('successful-payment-message');
  
        element.innerHTML = `
            ${_self.name} completed <br>
            Your payment ID is: <span class="payment-id">${paymentResponse.id}</span>
          `;
      },
      onError: async (_self, error) => {
        const element = document.getElementById('error-message');
  
        element.innerHTML = `
            ${_self.name} error <br>
            Error occurred: <pre class="error-object">${error}</pre>
          `;
      }
    });

    /*const applePay = checkoutWebComponents.create('applepay', {
      onPaymentCompleted: (_self, paymentResponse) => {
        const element = document.getElementById('successful-payment-message');
  
        element.innerHTML = `
            ${_self.name} completed <br>
            Your payment ID is: <span class="payment-id">${paymentResponse.id}</span>
          `;
      },
      onError: (_self, error) => {
        const element = document.getElementById('error-message');
        console.log(error);
  
        element.innerHTML = `
            ${_self.name} error <br>
            Error occurred: <pre class="error-object">${error}</pre>
          `;
      }
    });*/
  
    if (await flow.isAvailable()) {
      flow.mount(document.getElementById('payments'));
    }

    /*if (await applePay.isAvailable()) {
      applePay.mount(document.getElementById('applepay'));
      document.getElementById('interblock').style.display = 'flex';
    }*/
  })();
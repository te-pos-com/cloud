<script src="https://js.stripe.com/v3/"></script>

    <div class="form-row">
        <div id="card-element" class="card" style="width:100%;" >
          
          <b>Silahkan Melakukan Pembayaran Ke : </b>
          <br/>
          BANK : BCA<br/>
          NO REKENING : 4560123511<br/>
          ATAS NAMA : PRIYO SUBARKAH
          <br/>
          <br/>
          Setelah melakukan pembayaran<br/>
          Lakukan Konfirmasi Ke WA <br/>
          <b>081328675727</b>
          Atau dengan klik tombol dibawah ini<br/><br/>
          Terimakasih 
          <br/>
        </div>
    </div>
    
    <div class="form-row">
        <a href="https://wa.me/+6281328675727?text=Konfirmasi Pembayaran No Transaksi {{$payment_id}}"  class="btn btn-dark btn-block" id="pay_now"><i class="ti-credit-card"></i>Konfirmasi WA</a>
    </div>
    <br/>
    <div class="form-row">
        <a href="{{ route('dashboard') }}" class="btn btn-dark btn-block" id="pay_now"><i class="ti-credit-card"></i>Dashboard</a>
    </div>

<script>
// Create a Stripe client.
var stripe = "";

// Create an instance of Elements.
var elements = stripe.elements();

// Custom styling can be passed to options when creating an Element.
// (Note that this demo uses a wider set of styles than the guide below.)
var style = {
  base: {
    color: '#32325d',
    fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
    fontSmoothing: 'antialiased',
    fontSize: '16px',
    '::placeholder': {
      color: '#aab7c4'
    }
  },
  invalid: {
    color: '#fa755a',
    iconColor: '#fa755a'
  }
};

// Create an instance of the card Element.
var card = elements.create('card', {style: style});

// Add an instance of the card Element into the `card-element` <div>.
card.mount('#card-element');

// Handle real-time validation errors from the card Element.
card.on('change', function(event) {
  var displayError = document.getElementById('card-errors');
  if (event.error) {
    displayError.textContent = event.error.message;
  } else {
    displayError.textContent = '';
  }
});

// Handle form submission.
var form = document.getElementById('payment-form');
form.addEventListener('submit', function(event) {
  event.preventDefault();
  document.getElementById('pay_now').disabled = true;
  
  stripe.createToken(card).then(function(result) {
    if (result.error) {
      // Inform the user if there was an error.
      var errorElement = document.getElementById('card-errors');
      errorElement.textContent = result.error.message;
      document.getElementById('pay_now').disabled = false;
    } else {
      // Send the token to your server.
      stripeTokenHandler(result.token);
    }
  });
});

// Submit the form with the token ID.
function stripeTokenHandler(token) {
  // Insert the token ID into the form so it gets submitted to the server
  var form = document.getElementById('payment-form');
  var hiddenInput = document.createElement('input');
  hiddenInput.setAttribute('type', 'hidden');
  hiddenInput.setAttribute('name', 'stripeToken');
  hiddenInput.setAttribute('value', token.id);
  form.appendChild(hiddenInput);

  // Submit the form
  form.submit();
}

</script>
<h2>Checkout</h2>

<div class="checkout-container">
    <div id="cartSummary" class="cart-summary">
        <!-- Cart summary loaded  / dynamically -->
    </div>

    <div class="payment-section">
        <h3>Select Payment Method:</h3>
        <div class="payment-options">
            <label>
                <input type="radio" name="payment" id="cashOnDelivery" value="Cash on Delivery" checked> Cash on Delivery
            </label><br>
            <label>
                <input type="radio" name="payment" id="payWithCard" value="Pay with Card"> Pay with Card
            </label><br>
            <label>
                <input type="radio" name="payment" id="payWithKhalti" value="Pay with Khalti"> Pay with Khalti
            </label><br>
        </div>
    </div>

    <div class="shipping-details">
        <h3>Booking :</h3>
        <label>Name: <input type="text" id="shippingName" required></label><br>
        <label>Email: <input type="email" id="shippingEmail" required></label><br>
        <label>Phone: <input type="text" id="shippingPhone" required></label><br>
        <label>Address: <input type="text" id="shippingAddress" required></label><br>
    </div>

    <div class="confirm-payment-section">
        <button id="confirmPaymentBtn" class="btn-confirm">Confirm Payment</button>
    </div>
</div>


<!-- Include jQuery for AJAX functionality -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
<!-- JavaScript for loading cart and handling payment -->
$(document).ready(function () {
    // Load cart summary on the checkout page
    $.ajax({
        url: '/api/cart/GetCart1',
        method: 'GET',
        success: function (data) {
            let total = 0;
            $('#cartSummary').html('');
            data.cartDetails.forEach(item => {
                total += item.price * item.quantity; // Calculate total price
                $('#cartSummary').append(`
                    <div class="cart-item">
                        <h4>${item.product.name}</h4>
                        <p>Price: $${item.price}</p>
                        <p>Quantity: ${item.quantity}</p>
                    </div>
                `);
            });
            $('#cartSummary').append(`<h4 class="cart-total">Total: $${total}</h4>`);
        },
        error: function (xhr, status, error) {
            console.log("Error loading cart summary: " + error);
        }
    });

    // Handle payment confirmation
    $('#confirmPaymentBtn').click(async function () {
        let selectedPayment = $('input[name="payment"]:checked').val();
        let name = $('#shippingName').val();
        let email = $('#shippingEmail').val();
        let phone = $('#shippingPhone').val();
        let address = $('#shippingAddress').val();
        let totAmount = $('#cartSummary .cart-total').text().split('$')[1]; // Extract total amount
                let totalAmount = parseInt(totAmount*10, 10);  // Convert the string to an integer
        if (selectedPayment === "Pay with Khalti") {
            // Send request to Khalti API
            const response = await sendKhaltiPayment(totalAmount, name, email, phone);
            alert("Payment confirmed: " + response.status);
        } else {
            alert("Payment confirmed: " + selectedPayment);
        }

        // Optionally, implement further logic for other payment methods here
    });

async function sendKhaltiPayment(amount, name, email, phone) {
    const payload = {
        return_url: "http://localhost:5268/home",
        website_url: "http://localhost:5268/",
        amount: amount,
        purchase_order_id: "Order01",
        purchase_order_name: "test",
        customer_info: {
            name: name,
            email: email,
            phone: phone
        }
    };

    try {
        const response = await fetch("http://localhost:5268/api/payment/khalti", { 
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify(payload)
        });

                    if (!response.ok) {
                        console.error("Error: ", response.statusText);
                        document.getElementById("response").innerText = "Payment failed: " + response.statusText;
                        return;
                    }
                    const data = await response.json();
                    console.log(data);

                    // Show the response in the UI
                    document.getElementById("response").innerText = "Payment initiated successfully";

                    // If the response contains a paymentUrl, open it in a new tab
                    if (data.paymentUrl) {
                        window.open(data.paymentUrl, '_blank');
                    }
                    debugger;


    } catch (error) {
        // Log network or other fetch errors
        console.error("Failed to send request:", error);
    }
}

});
</script>

<!-- Styles for the page -->
<style>
    /* General page styles */
    body {
        font-family: Arial, sans-serif;
        background-color: #f7f7f7;
        margin: 0;
        padding: 20px;
    }

    h2, h3 {
        color: #333;
        margin-bottom: 20px;
    }

    .checkout-container {
        display: grid;
        grid-template-columns: 1fr 1fr 1fr;
        gap: 20px;
        margin: auto;
        max-width: 1200px;
    }

    /* Cart summary styling */
    .cart-summary {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .cart-summary h4 {
        margin: 0 0 10px 0;
    }

    .cart-item {
        padding: 10px 0;
        border-bottom: 1px solid #ddd;
    }

    .cart-item:last-child {
        border-bottom: none;
    }

    .cart-total {
        font-size: 18px;
        font-weight: bold;
        margin-top: 10px;
    }

    /* Payment section styling */
    .payment-section {
        background-color: white;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
    }

    .payment-options label {
        display: block;
        margin-bottom: 10px;
        font-size: 16px;
        cursor: pointer;
    }

    /* Confirm payment button styling */
    .confirm-payment-section {
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .btn-confirm {
        padding: 15px 30px;
        background-color: #28a745;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-size: 16px;
        transition: background-color 0.3s;
    }

    .btn-confirm:hover {
        background-color: #218838;
    }


</style>

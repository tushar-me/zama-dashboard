<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Order Confirmation Mail</title>
</head>
<body>
	<div style="background-color: white; padding: 16px;">
		<div style="margin-bottom: 24px;">
		  <h3 style="color: black; font-size: 20px; font-weight: 500; margin: 0;">
		  	Dear {{$customer}},
		  </h3>
		  <p>Thank you for shopping with us! 🛍️
		    <br>
		  We have received your order.</p>

		  <p>Order Number: <span style="color: #4C2746; font-weight:700;">{{$order_code}}</span></p>
		  <p>Your order is now being packed with care and will be shipped out soon. You can track your order anytime from your dashboard on our website: <br> <br> <a style="color: #4C2746; font-weight:700;" href="https://www.pixelsultra.com/dashboard" target="_blank">Track Order</a></p>
		  <p>If you have any questions or need assistance, feel free to:</p>
		  <p>📞 Call or WhatsApp us: 01972636297 / 01752898448</p>
		  <p>💬 Message us on:</p>
		  <p>Facebook – <a style="color: #4C2746; font-weight:700;" href="https://www.tentomart.com/" target="_blank">facebook.com/pixelsultra</a></p>
		  <p>Instagram – <a style="color: #4C2746; font-weight:700" href="https://www.tentomart.com/" target="_blank">instagram.com/pixels.ultra</a></p>
		  <br>
		 
		  <p>
			<br>
			Warm regards,
			<br>
			Business Name
		  </p>
		</div>
	  </div>
</body>
</html>
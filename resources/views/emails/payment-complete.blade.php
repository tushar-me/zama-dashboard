<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Payment Confirmation</title>
</head>
<body>
	<div style="background-color: white; padding: 16px;">
		<div style="margin-bottom: 24px;">
			<h3 style="color: black; font-size: 20px; font-weight: 500; margin: 0;">
				Dear {{$customer}},
			</h3>

			<p>We’re happy to inform you that your payment has been successfully received. 🎉</p>

			<p>
				Order Number: <span style="color: #4C2746; font-weight:700;">{{$order_code}}</span><br>
				Payment Amount: <span style="color: #4C2746; font-weight:700;">{{$amount}}</span><br>
				Payment Method: <span style="color: #4C2746; font-weight:700;">{{$payment_method}}</span><br>
				Transaction ID: <span style="color: #4C2746; font-weight:700;">{{$transaction_id}}</span>
			</p>

			<p>Your order is now being processed and will be shipped soon.  
			You can check your order details anytime from your dashboard:</p>

			<p>
				<a style="color: #4C2746; font-weight:700;" href="https://www.pixelsultra.com/dashboard" target="_blank">
					View Order
				</a>
			</p>
			<br>
			<p>If you have any questions or need help, feel free to reach out:</p>
			<p>📞 Call or WhatsApp us: 01972636297 / 01752898448</p>
			<p>💬 Message us on:</p>
			<p>Facebook – <a style="color: #4C2746; font-weight:700;" href="https://www.facebook.com/pixelsultra" target="_blank">facebook.com/pixelsultra</a></p>
			<p>Instagram – <a style="color: #4C2746; font-weight:700;" href="https://www.instagram.com/pixels.ultra/" target="_blank">instagram.com/pixels.ultra</a></p>

			<br><br>
			<p>
				Thank you for your trust and payment. 💖<br>
				Warm regards,<br>
				<b>Pixels Ultra</b>
			</p>
		</div>
	</div>
</body>
</html>

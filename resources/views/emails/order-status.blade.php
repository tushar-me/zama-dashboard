<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Order Status Update</title>
</head>
<body>
	<div style="background-color: white; padding: 16px;">
		<div style="margin-bottom: 24px;">
			<h3 style="color: black; font-size: 20px; font-weight: 500; margin: 0;">
				Dear {{$customer}},
			</h3>

			<p>We wanted to let you know that the status of your order has been updated 🛒</p>

			<p>
				Order Number: <span style="color: #4C2746; font-weight:700;">{{$order_code}}</span>
				<br>
				Current Status: <span style="color: #4C2746; font-weight:700;">{{$order_status}}</span>
			</p>

			<p>
				You can track your order anytime from your dashboard on our website:
				<br><br>
				<a style="color: #4C2746; font-weight:700;" href="https://tentomart.com/dashboard" target="_blank">Track Order</a>
			</p>

		

			<p>If you have any questions or need assistance, feel free to:</p>
			<p>📞 Call or WhatsApp us: 01972636297 / 01752898448</p>
			<p>💬 Message us on:</p>
			<p>Facebook – <a style="color: #4C2746; font-weight:700;" href="https://www.facebook.com/pixelsultra" target="_blank">facebook.com/pixelsultra</a></p>
			<p>Instagram – <a style="color: #4C2746; font-weight:700" href="https://www.instagram.com/pixels.ultra/" target="_blank">instagram.com/pixels.ultra</a></p>

			<br><br>
			<p>
				Thank you for shopping with us!
				<br>
				Warm regards,
				<br>
				<b>Pixels Ultra</b>
			</p>
		</div>
	</div>
</body>
</html>

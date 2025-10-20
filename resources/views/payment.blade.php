<html>
<head>
    <title>Test Payment</title>
</head>
<body>
    <h1>Test Midtrans Payment</h1>
    <form action="/payment" method="POST">
        @csrf
        <label>Amount:</label>
        <input type="number" name="amount" value="10000" required>
        <button type="submit">Pay Now</button>
    </form>
</body>
</html>

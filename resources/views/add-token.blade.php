<form action="/add-initial-token" method="POST">
    @csrf
    <input type="number" name="user_id" placeholder="User ID" required><br>
    <input type="text" name="ml_account_id" placeholder="ML Account ID" required><br>
    <input type="text" name="access_token" placeholder="Access Token" required><br>
    <input type="text" name="refresh_token" placeholder="Refresh Token" required><br>
    <input type="number" name="expires_in" placeholder="Expires In (seconds)" value="21600" required><br>
    <button type="submit">Guardar Token</button>
</form>

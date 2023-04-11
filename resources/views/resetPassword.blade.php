<form action="" method="post">
    @csrf
    <input type="hidden" name="id" value="{{$user[0]['id']}}">
    <input type="password" name="password" placeholder="New password">
    <br><br>
    <input type="password" name="password_confirmation" placeholder="Confirm Password">
    <input type="submit" value="save">
</form>
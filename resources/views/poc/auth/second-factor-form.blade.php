@extends('layouts.empty')

@section('content')
    <h1>Enter Verification Code</h1>
    <p>Please enter the verification value sent.</p>

    <form>
        <p>
            <label for="code">Code:</label>
            <input type="text" name="code" id="code">
        </p>
        <input type="submit" value="Verify">
    </form>

@endsection

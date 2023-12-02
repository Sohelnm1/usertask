<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="{{ asset('css/register.css') }}">

    <title>Document</title>
</head>

<body>
    <div class="register-form">
        <form id="registrationForm" method="POST" action="{{ url('/api/register') }}">
            <h1> Register </h1>
            <div id="message" class="alert" role="alert">
            </div>
            @csrf
            <div class="form-group">
                <label for="name">Name</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Enter name">
            </div>
            <div class="form-group">
                <label for="email">Email address</label>
                <input type="email" class="form-control" id="email" name="email" aria-describedby="emailHelp"
                    placeholder="Enter email">
                <small id="emailHelp" class="form-text text-muted">We'll never share your email with anyone
                    else.</small>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
            <button type="submit" class="btn btn-primary">Submit</button>
        </form>
    </div>
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"
        integrity="sha384-q8i/X+965DzO0rT7abK41JStQIAqVgRVzpbzo5smXKp4YfRvH+8abtTE1Pi6jizo" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.14.7/dist/umd/popper.min.js"
        integrity="sha384-UO2eT0CpHqdSJQ6hJty5KVphtPhzWj9WO1clHTMGa3JDZwrnQq4sF86dIHNDz0W1" crossorigin="anonymous">
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"
        integrity="sha384-JjSmVgyd0p3pXB1rRibZUAYoIIy6OrQ6VrjIEaFf/nJGzIxFDsf4x0xIM+B07jRM" crossorigin="anonymous">
    </script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <script>
    $(document).ready(function() {
        $("#registrationForm").submit(function(e) {
            e.preventDefault();

            // Set a permanent usertype value
            var usertype = "user";

            // Serialize form data including usertype
            var formData = $("#registrationForm").serialize() + "&usertype=" + usertype;

            $.ajax({
                type: "POST",
                url: "/api/register",
                data: formData,
                success: function(response) {
                    if (response.status === "success") {
                        window.location.href = "/dashboard";
                    } else {
                        $("#message").html(
                            "<div class='alert alert-danger'>" + response.message +
                            "</div>"
                        );
                    }
                },
                error: function(xhr, status, error) {
                    var message = xhr.responseText ? JSON.parse(xhr.responseText).message :
                        "An error occurred.";
                    $("#message").html(
                        "<div class='alert alert-danger'>" + message + "</div>"
                    );
                },
            });
        });
    });
    </script>

</body>

</html>
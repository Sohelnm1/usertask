<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Document</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
</head>

<body>
    @if(auth()->check())
    <nav class="navbar navbar-light" style="background-color: #e3f2fd;">

        <a class="navbar-brand" href="#">
            <h1>Hello, {{ auth()->user()->name }}</h1>
        </a>
        <form id="logoutForm">
            @csrf
            <button type="submit" class="btn btn-primary">Logout</button>
        </form>
    </nav>
    <form id="createtaskForm" method="POST">
        @csrf
        <h1> Create Task </h1>
        <div id="message" class="alert" role="alert">
        </div>
        <div class="form-group">
            <label for="title">Title</label>
            <input type="text" class="form-control" id="title" name="title" placeholder="Enter title">
        </div>
        <div class="form-group">
            <label for="description">Description</label>
            <input type="text" class="form-control" id="description" name="description" placeholder="Enter description">
        </div>
        <button type="submit" class="btn btn-primary">Create</button>
    </form>
    @else
    <script>
    window.location = "{{ route('login') }}";
    </script>
    @endif
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
        $("#createtaskForm").submit(function(e) {
            e.preventDefault();

            // Set a permanent usertype value
            var status = "incomplete";

            // Serialize form data including usertype
            // var formData = $("#createtaskForm").serialize() + "&status=" + status;
            var formData = $("#createtaskForm").serialize() + "&status=" + status;
            formData += "&_token=" + $("meta[name='csrf-token']").attr("content");

            $.ajax({
                type: "POST",
                url: "/api/creatertask",
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
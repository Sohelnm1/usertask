<!DOCTYPE html>
<html lang="en">

<head>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css"
        integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
    /* Add this style for the modal overlay */
    .modal-overlay {
        display: none;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        justify-content: center;
        align-items: center;
    }

    /* Add this style for the modal content */
    .modal-content {
        background-color: #fff;
        padding: 20px;
        border-radius: 8px;
        max-width: 400px;
        width: 100%;
        text-align: center;
    }
    </style>
</head>


<body>

    <nav class="navbar navbar-light" style="background-color: #e3f2fd;">
        @if(auth()->check())
        <a class="navbar-brand" href="#">
            <h1>Hello, {{ auth()->user()->name }}</h1>
        </a>
        <form id="logoutForm">
            @csrf
            <button type="submit" class="btn btn-primary">Logout</button>
        </form>
        @else
        <script>
        window.location = "{{ route('login') }}";
        </script>
        @endif
    </nav>
    <div class="task-body">
        <button class="btn btn-primary" type="button" onclick="window.location.href='{{ url('createtask') }}'">Create
            Task</button>

        <select id="statusFilter">
            <option value="">All</option>
            <option value="incomplete">Incomplete</option>
            <option value="complete">Complete</option>
        </select>

        <button id="filterTasks" class="btn btn-primary">Filter</button>

        <table class="table mt-4">
            <thead>
                <tr>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Status</th>
                    <th>Owner's Name</th>
                    <th>Action</th>
                    <th>Edit</th>

                </tr>
            </thead>
            <tbody id="taskListBody">
            </tbody>
        </table>
        <div id="paginationLinks"></div>
    </div>

    <div class="modal-overlay" id="editTaskModalOverlay">
        <div class="modal-content">
            <h5>Edit Task</h5>
            <form id="editTaskForm">
                <div class="form-group">
                    <label for="editTitle">Title:</label>
                    <input type="text" class="form-control" id="editTitle" name="title" required>
                </div>
                <div class="form-group">
                    <label for="editDescription">Description:</label>
                    <textarea class="form-control" id="editDescription" name="description" rows="3" required></textarea>
                </div>
                <input type="hidden" id="editTaskId" name="taskId">
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
            <button class="btn btn-secondary" onclick="closeEditTaskModal()">Close</button>
        </div>
    </div>

    <!-- Include jQuery first -->
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

    <!-- Include Popper.js -->
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>

    <!-- Include the full version of Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/js/bootstrap.min.js"></script>






    <script>
    $(document).ready(function() {
        fetchTaskData();

        $("#logoutForm").submit(function(e) {
            e.preventDefault();

            function showEditTaskModal() {
                $("#editTaskModalOverlay").show();
            }

            // Function to close the modal
            function closeEditTaskModal() {
                $("#editTaskModalOverlay").hide();
            }

            $.ajax({
                type: "POST",
                url: "/api/logout",
                data: $("#logoutForm").serialize(),
                success: function(response) {
                    window.location.href = "/login";
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

        $("#filterTasks").click(function() {
            fetchTaskData($("#statusFilter").val());
        });

        function fetchTaskData(statusFilter = "", page = 1) {
            $.ajax({
                type: "GET",
                url: "/api/tasks",
                data: {
                    status: statusFilter,
                    page: page,
                },
                success: function(response) {
                    $("#taskListBody").empty();

                    if (response.tasks.data.length > 0) {
                        $.each(response.tasks.data, function(index, task) {
                            $("#taskListBody").append(
                                "<tr>" +
                                "<td>" + task.title + "</td>" +
                                "<td>" + task.description + "</td>" +
                                "<td>" + task.status + "</td>" +
                                "<td>" + (task.user ? task.user.name : 'N/A') +
                                "</td>" +
                                "<td>" +
                                "<button class='btn btn-danger' data-task-id='" + task
                                .id + "'>Delete</button>" +
                                "<button class='btn btn-success ml-2' data-task-id='" +
                                task.id + "' data-status='" +
                                (task.status === 'complete' ? 'incomplete' :
                                    'complete') + "'>" +
                                (task.status === 'complete' ? 'Mark as Incomplete' :
                                    'Mark as Complete') + "</button>" +
                                "</td>" +
                                "<td>" +
                                "<button class='btn btn-primary btn-edit' data-task-id='" +
                                task.id + "'>Edit</button>" +
                                "</td>" +
                                "</tr>"
                            );
                        });

                        $(".btn-danger").click(function() {
                            var taskId = $(this).data("task-id");
                            deleteTask(taskId);
                        });

                        $(".btn-success").click(function() {
                            var taskId = $(this).data("task-id");
                            var newStatus = $(this).data("status");
                            updateTaskStatus(taskId, newStatus);
                        });
                    } else {
                        $("#taskListBody").append(
                            "<tr>" +
                            "<td colspan='4'>No tasks found</td>" +
                            "</tr>"
                        );
                    }

                    // Display pagination links
                    displayPaginationLinks(response.tasks);
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching task data:", error);
                },
            });
        }

        function updateTaskStatus(taskId) {
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var headers = {
                'X-CSRF-TOKEN': csrfToken,
            };

            $.ajax({
                type: "PATCH", // Use PATCH method for updating
                headers: headers,
                url: "/api/tasks/" + taskId + "/update-status",
                success: function(response) {
                    fetchTaskData($("#statusFilter").val());
                },
                error: function(xhr, status, error) {
                    console.error("Error updating task status:", error);
                },
            });
        }

        function displayPaginationLinks(tasks) {
            $("#paginationLinks").empty();

            if (tasks.links) {
                // Add the pagination links to #paginationLinks
                $.each(tasks.links, function(index, link) {
                    var label = link.label;
                    var active = link.active;

                    if (link.url) {
                        // Create a button element with unescaped HTML
                        var buttonElement = $("<button></button>").html(label);

                        // Add a click event to the button only if it's not the current active page
                        if (!active) {
                            buttonElement.click(function() {
                                // Extract the page number from the URL
                                var pageNumber = extractPageNumberFromUrl(link.url);

                                // Call fetchTaskData with the extracted page number
                                fetchTaskData($("#statusFilter").val(), pageNumber);
                            });
                        }

                        $("#paginationLinks").append(buttonElement);
                    } else {
                        // If the URL is null, it's the "Previous" or "Next" button
                        var buttonElement = $("<button></button>").html(label);

                        // Disable the button if it's inactive
                        if (!active) {
                            buttonElement.attr("disabled", true);
                        }

                        // Append the button to #paginationLinks
                        $("#paginationLinks").append(buttonElement);
                    }
                });
            }
        }


        function extractPageNumberFromUrl(url) {
            // Use regex to extract the page number from the URL
            var match = url.match(/page=(\d+)/);

            // Return the extracted page number or a default value (1 in this case)
            return match ? parseInt(match[1]) : 1;
        }


        function deleteTask(taskId) {
            var csrfToken = $('meta[name="csrf-token"]').attr('content');
            var headers = {
                'X-CSRF-TOKEN': csrfToken,
            };
            $.ajax({
                type: "DELETE",
                headers: headers,
                url: "/api/tasks/" +
                    taskId,
                success: function(response) {
                    fetchTaskData($("#statusFilter").val());
                },
                error: function(xhr, status, error) {
                    console.error("Error deleting task:", error);
                },
            });
        }

        $(".btn-edit").click(function() {
            var taskId = $(this).data("task-id");
            // Fetch task details and populate the modal with current values
            fetchTaskDetails(taskId);
            // Show the custom modal
            showEditTaskModal();
        });

        // Function to fetch task details and populate the edit modal
        function fetchTaskDetails(taskId) {
            $.ajax({
                type: "GET",
                url: "/api/tasks/" + taskId,
                success: function(response) {
                    // Populate modal fields with task details
                    $("#editTitle").val(response.task.title);
                    $("#editDescription").val(response.task.description);
                    // Populate other fields as needed

                    // Set the task ID in a hidden field for later use
                    $("#editTaskId").val(taskId);

                    // Show the modal
                    $("#editTaskModal").modal("show");
                },
                error: function(xhr, status, error) {
                    console.error("Error fetching task details:", error);
                },
            });
        }

        // Handle the form submission for editing a task
        $("#editTaskForm").submit(function(e) {
            e.preventDefault();

            var taskId = $("#editTaskId").val();
            // Additional fields to update can be retrieved from the form

            // Perform AJAX request to update the task details
            $.ajax({
                type: "PATCH",
                url: "/api/tasks/" + taskId,
                data: $("#editTaskForm").serialize(),
                success: function(response) {
                    // Close the modal
                    $("#editTaskModal").modal("hide");
                    // Refresh the task list
                    fetchTaskData($("#statusFilter").val());
                },
                error: function(xhr, status, error) {
                    console.error("Error updating task details:", error);
                },

            });
            closeEditTaskModal();
        });
    });
    </script>

</body>

</html>
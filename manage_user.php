<?php
include_once("./database/constants.php");
if (!isset($_SESSION["userid"])) {
    header("location:".DOMAIN."/");
}
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Billing System</title>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.3/umd/popper.min.js" integrity="sha384-vFJXuSJphROIrBnz7yo7oB41mKfc8JzQZiCq4NCceLEaO4IHwicKwpJf9c9IpFgh" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js" integrity="sha384-alpBpkh1PFOepccYVYDB4do5UnbKysX5WZXm3XxPqe5iKTfUKjNkCk9SaVuEZflJ" crossorigin="anonymous"></script>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css" integrity="sha384-PsH8R72JQ3SOdhVi3uxftmaW6Vc51MKb0q5P2rRUpPvrszuE4W1povHYgTpBfshb" crossorigin="anonymous">
    <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script type="text/javascript" src="./js/manage.js"></script>
</head>
<body>
    <!-- Navbar -->
    <?php include_once("./templates/header.php"); ?>
    <br/><br/>
    <div class="container">
        <table class="table table-hover table-bordered">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>User Type</th>
                    <th>Last Login</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="get_user">
                <?php
                // Database query and user retrieval
                include_once("./database/db.php");
                $db = new Database();
                $conn = $db->connect();
                if ($conn === "DATABASE_CONNECTION_FAIL") {
                    die("Database connection failed");
                }
                $sql = "SELECT * FROM user WHERE usertype IN ('admin', 'staff')";
                $result = $conn->query($sql);
                if ($result->num_rows > 0) {
                    $i = 1;
                    while ($row = $result->fetch_assoc()) {
                        ?>
                        <tr>
                            <td><?php echo $i; ?></td>
                            <td><?php echo $row["username"]; ?></td>
                            <td><?php echo $row["email"]; ?></td>
                            <td><?php echo $row["usertype"]; ?></td>
                            <td><?php echo $row["last_login"]; ?></td>
                            <td>
                                <a href="#" did="<?php echo $row['id']; ?>" class="btn btn-sm btn-info edit_user">Edit</a>
                                <?php if ($row["id"] != $_SESSION["userid"]) { ?>
                                    <a href="#" did="<?php echo $row['id']; ?>" class="btn btn-sm btn-danger delete_user">Delete</a>
                                <?php } ?>
                            </td>
                        </tr>
                        <?php
                        $i++;
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="6" align="center">No users found</td>
                    </tr>
                    <?php
                }
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal for confirmation -->
    <div class="modal fade" id="deleteModal" tabindex="-1" role="dialog" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="deleteModalLabel">Delete User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Are you sure you want to delete this user?</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript code -->
    <script>
        $(document).ready(function() {
            // Delete button click event
            $(".delete_user").click(function() {
                var userId = $(this).attr("did");
                $("#confirmDelete").attr("did", userId);
                $("#deleteModal").modal("show");
            });

            // Confirm delete button click event
            $("#confirmDelete").click(function() {
                var userId = $(this).attr("did");
                $.ajax({
                    url: "delete_user.php", // PHP file to handle the delete action
                    method: "POST",
                    data: {id: userId},
                    success: function(data) {
                        if (data == "DELETED") {
                            $("#deleteModal").modal("hide");
                            // Refresh user table
                            $.ajax({
                                url: "fetch_users.php", // PHP file to retrieve updated user table
                                method: "POST",
                                success: function(response) {
                                    $("#get_user").html(response);
                                }
                            });
                        }
                    }
                });
            });
        });
    </script>
</body>
</html>

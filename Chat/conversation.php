<?php
include '../config.php';
// Perform query
/** @var TYPE_NAME $conn */
session_start();
$userid = $_SESSION["current_user"]["u_id"];
$partnerId = null;
if (isset($_GET['partner'])) {
    $partnerId = $_GET['partner'];
}

$query = "select * FROM tbl_chat WHERE id in (SELECT max(id) FROM tbl_chat WHERE tbl_chat.use_id_1 = $userid OR tbl_chat.use_id_2 = $userid GROUP BY use_id_1, use_id_2) ORDER by id DESC ";
$res = $conn->query($query);

$conversation = [];
$a = 0;
while($row = $res->fetch_array()):
    $partnerIdCon = $row["use_id_1"] == $userid ? $row["use_id_2"] : $row["use_id_1"];
    if ($partnerId == null && $a == 0) {
        $partnerId = $partnerIdCon;
        $a += 1;
    }
    $conversation[$partnerIdCon] = $row;
endwhile;

$_SESSION["userid"] = $userid;
$_SESSION["chat"]["partnerId"] = $partnerId;

?>

<!DOCTYPE html>
<html lang="en">
<html>
<head>
    <link rel="stylesheet" href="./conversation.php">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>::Message::</title>
    <link rel="stylesheet" href="chat.css">
    <script>
        function ajax() {
            var req =  new XMLHttpRequest();
            req.onreadystatechange = function() {
                if (req.readyState == 4 && req.status == 200) {
                    document.getElementById('chat').innerHTML = req.responseText;
                }
            }
            req.open('GET','chat.php?partner=<?php echo $partnerId ?>', true);
            req.send();
        }
        setInterval(function(){ajax()},1000);
    </script>
    <?php include "../partials/html_header.php"; ?>
</head>
<body>
<?php include "../partials/aside.php"; ?>
<main class="admin-main">
    <?php include "../partials/header.php"; ?>
<div class="container m-t-30">
    <div class="messaging">
        <div class="inbox_msg">
            <div class="inbox_people">
                <div class="headind_srch">
                    <div class="recent_heading">
                        <h2>Message Recent</h2>
                    </div>
                    <div class="srch_bar">
                        <div class="stylish-input-group">
                            <input type="text" class="search-bar" placeholder="Search">
                            <span class="input-group-addon">
                <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                </span></div>
                    </div>
                </div>
                <div class="inbox_chat">
<!--                    -->
                    <?php
                    foreach ($conversation as $row) {
                        $partnerIdCon = $row["use_id_1"] == $userid ? $row["use_id_2"] : $row["use_id_1"];
                        $name = mysqli_fetch_array($conn->query("SELECT user.username FROM user where user.u_id = $partnerIdCon"), MYSQLI_ASSOC);
                        if ($partnerIdCon == $partnerId) {
                        ?>

                        <div class="chat_list active_chat">
                            <div class="chat_people">
                                <div class="chat_ib">
                                    <h5><?= $name["username"] ?>
                                        <span class="chat_date">
                                            <?php echo formatDate($row['date']);?>
                                        </span>
                                    </h5>
                                    <p><?php echo $row['message'];?></p>
                                </div>
                            </div>
                        </div>
                    <?php
                        } else {
                            ?>
                    <a href="conversation.php?partner=<?php echo $partnerIdCon ?>">
                            <div class="chat_list">
                                <div class="chat_people">
                                    <div class="chat_ib">
                                        <h5><?= $name["username"] ?>
                                            <span class="chat_date">
                                            <?php echo formatDate($row['date']);?>
                                        </span>
                                        </h5>
                                        <p><?php echo $row['message'];?></p>
                                    </div>
                                </div>
                            </div>
                    </a>
                    <?php
                        }
                    }
                    ?>
<!--                    -->
                </div>
            </div>
            <div class="mesgs">
                <div class="msg_history" id="chat">

                </div>
                <div class="type_msg">
                    <form action="" method="post">
                        <div class="input_msg_write">
                            <label for="message">Chat:</label><br>
                            <textarea name="message" id="message-write" class="write_msg" placeholder="Type a message"></textarea><br>
                        </div>
                        <input input type="submit" name="submit" value="Send" class="msg_send_btn"><i class="fa fa-paper-plane-o" aria-hidden="true" value="submit"></i></input>
                    </form>
                    <?php
                    if (isset($_POST['submit'])) {
                        $message = $_POST['message'];
                        if ($message != null) {
                            $query = "INSERT INTO tbl_chat (use_id_1, use_id_2, message) VALUES ('$userid','$partnerId', '$message')";
                            $run = $conn->query($query);
                            if ($run) {
                                echo "<embed loop='false' src='plink.wav' hidden='true' autoplay='true'/>";
                            }
                        }
                    }
                    ?>
                </div>
            </div>
        </div>

    </div>
</div>
</main>
</body>
</html>
<!---->
<!--<!DOCTYPE html>-->
<!--<html lang="en">-->
<!--<head>-->
<!--    <meta charset="UTF-8">-->
<!--    <meta name="viewport" content="width=device-width, initial-scale=1.0">-->
<!--    <meta http-equiv="X-UA-Compatible" content="ie=edge">-->
<!--    <title>::Message::</title>-->
<!--    <link rel="stylesheet" href="chat.css">-->
<!--    <script>-->
<!--        function ajax() {-->
<!--            var req =  new XMLHttpRequest();-->
<!--            req.onreadystatechange = function() {-->
<!--                if (req.readyState == 4 && req.status == 200) {-->
<!--                    document.getElementById('chat').innerHTML = req.responseText;-->
<!--                }-->
<!--            }-->
<!--            req.open('GET','chat.php', true);-->
<!--            req.send();-->
<!--        }-->
<!--        setInterval(function(){ajax()},1000);-->
<!--    </script>-->
<!--</head>-->
<!--<body onload="ajax();">-->
<!---->
<!--<div class="page">-->
<!--    <div class="display-box">-->
<!--        <div id="chat"></div>-->
<!--    </div>-->
<!--    <div class="form">-->
<!--        <form action="" method="post">-->
<!--            <label for="message">Chat:</label><br>-->
<!--            <textarea name="message" id="message-write" cols="30" rows="3"></textarea><br>-->
<!--            <input type="submit" name="submit" value="Send">-->
<!--        </form>-->
<!--        --><?php
//        if (isset($_POST['submit'])) {
//
//            $message = $_POST['message'];
//            if ($message != null) {
//                $query = "INSERT INTO tbl_chat (use_id_1, use_id_2, message) VALUES ('$userid','$partnerId', '$message')";
//                $run = $conn->query($query);
//                if ($run) {
//                    echo "<embed loop='false' src='plink.wav' hidden='true' autoplay='true'/>";
//                }
//            }
//        }
//        ?>
<!--    </div>-->
<!--</div>-->
<!---->
<!--</body>-->
<!--</html>-->
<!---->

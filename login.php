<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id = $_POST["id"];
    $pass = $_POST["pass"];

    $con = mysqli_connect("localhost", "user", "12345", "sample");

    if (!$con) {
        die("연결 오류: " . mysqli_connect_error());
    }

    $sql = "SELECT * FROM 회원 WHERE 로그인_ID='$id'";
    $result = mysqli_query($con, $sql);

    if (!$result) {
        die("쿼리 오류: " . mysqli_error($con));
    }

    $num_match = mysqli_num_rows($result);

    if (!$num_match) {
        echo "<script>
            alert('등록되지 않은 아이디입니다!');
            history.go(-1);
          </script>";
    } else {
        $row = mysqli_fetch_assoc($result);
        $db_pass = $row["비밀번호"];

        mysqli_close($con);

        if ($pass != $db_pass) {
            echo "<script>
                alert('비밀번호가 틀립니다!');
                history.go(-1);
              </script>";
            exit;
        } else {
            // 로그인 성공 시 사용자 정보 세션에 저장
            session_start();
            $_SESSION["userid"] = $row["로그인_ID"];
            $_SESSION["username"] = $row["이름"];

            // 로그인 성공 시 페이지 이동
            echo "<script>
                location.href = 'index.php';
              </script>";
        }
    }
}
?>
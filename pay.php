<?php
session_start(); // 세션 시작

$servername = "localhost";
$username = "user";
$password = "12345";
$dbname = "sample";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("연결 실패: " . $conn->connect_error);
}

function generateUniqueOrderID($conn) {
    $orderID = '';

    do {
        $orderNumber = generateOrderNumber();
        $orderID = 'A' . $orderNumber;

        $checkDuplicateQuery = "SELECT COUNT(*) AS count FROM 주문 WHERE 주문_ID = '$orderID'";
        $result = $conn->query($checkDuplicateQuery);
        $row = $result->fetch_assoc();
        $count = $row['count'];

    } while ($count > 0);

    return $orderID;
}

function generateOrderNumber() {
    // 원하는 주문 ID 패턴에 따라 숫자를 생성하는 로직 추가
    // 여기서는 일단 랜덤 숫자 생성으로 대체
    return rand(10, 99);
}

function getMenuName($conn, $menuID) {
    $menuNameQuery = "SELECT 메뉴이름 FROM 메뉴 WHERE 메뉴_ID = '$menuID'";
    $menuNameResult = $conn->query($menuNameQuery);
    if ($menuNameResult->num_rows > 0) {
        $menuNameRow = $menuNameResult->fetch_assoc();
        return $menuNameRow['메뉴이름'];
    } else {
        return "메뉴 없음";
    }
}

function getMenuPrice($conn, $menuID) {
    $menuPriceQuery = "SELECT 가격 FROM 메뉴 WHERE 메뉴_ID = '$menuID'";
    $menuPriceResult = $conn->query($menuPriceQuery);
    if ($menuPriceResult->num_rows > 0) {
        $menuPriceRow = $menuPriceResult->fetch_assoc();
        return $menuPriceRow['가격'];
    } else {
        return 0;
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // 주문 정보를 세션에서 가져오기
    $storeInfo = $_SESSION['store_info'];
    $menuInfo = $_SESSION['menu_info'];

    if (empty($storeInfo)) {
        echo "<p>가게 정보를 찾을 수 없습니다.</p>";
        exit;
    }

    // 주문 정보를 데이터베이스에 저장
    $storeID = $storeInfo['가게_ID'];

    // 세션에서 사용자 ID 가져오기
    $loginID = isset($_SESSION['userid']) ? $_SESSION['userid'] : '';

    if (empty($loginID)) {
        echo "<p>로그인 정보를 찾을 수 없습니다.</p>";
        exit;
    }

    $paymentMethod = isset($_POST['payment_method']) ? $_POST['payment_method'] : '';

    // 요청사항 가져오기
    $orderRequest = isset($_POST['order_request']) ? $_POST['order_request'] : '';

    // 주문 ID 생성
    $orderID = generateUniqueOrderID($conn);

    // 주문 테이블에 주문 정보 추가
    $insertOrderQuery = "INSERT INTO 주문 (주문_ID, 가게_ID, 로그인_ID, 결제수단, 요청사항) VALUES ('$orderID', '$storeID', '$loginID', '$paymentMethod', '$orderRequest')";

    if ($conn->query($insertOrderQuery) === TRUE) {
        // 주문 메뉴 정보를 데이터베이스에 저장
        foreach ($_POST as $key => $value) {
            if (strpos($key, '_qty') !== false && (int)$value > 0) {
                $menuID = str_replace('_qty', '', $key);
                $quantity = (int)$value;
                // 메뉴 ID를 가져오기 위한 hidden field의 이름
                $menuIDFieldName = $menuID . '_id';
                $menuIDValue = isset($_POST[$menuIDFieldName]) ? $_POST[$menuIDFieldName] : '';

                // 메뉴 가격 가져오기
                $menuPrice = getMenuPrice($conn, $menuID);

                // 주문 메뉴 정보를 데이터베이스에 저장
                $insertOrderMenuQuery = "INSERT INTO 주문메뉴 (주문_ID, 메뉴_ID, 주문메뉴가격, 수량) VALUES ('$orderID', '$menuID', '$menuPrice', '$quantity')";

                if ($conn->query($insertOrderMenuQuery) !== TRUE) {
                    echo "Error: " . $insertOrderMenuQuery . "<br>" . $conn->error;
                }
            }
        }

        // 쿠폰 정보를 가져와 처리 
        if (isset($_POST['coupons']) && is_array($_POST['coupons'])) {
            foreach ($_POST['coupons'] as $coupon) {
                // 쿠폰 처리 로직 추가
            }
        }

        echo "<p>주문이 완료되었습니다. 주문 번호: $orderID</p>";

        // 세션에 주문 ID 저장
        $_SESSION['order_id'] = $orderID;
    } else {
        echo "Error: " . $insertOrderQuery . "<br>" . $conn->error;
    }
}

// 데이터베이스 연결 종료
$conn->close();
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>결제 완료</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        section {
            background-color: white;
            padding: 20px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            text-align: center;
        }

        h2 {
            color: #333;
            margin-bottom: 10px;
        }

        p {
            color: #555;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            justify-content: center;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<section>
    <h2>결제 완료 감사합니다</h2>
    <p>주문해 주셔서 감사합니다!</p>
    <form action="index.php" method="post">
        <input type="submit" value="추가 주문">
    </form>
    <form action="login_form.php" method="post">
        <input type="submit" value="주문 종료">
    </form>
</section>
<section>
<style>
        #deliveryStatus {
            font-size: 18px;
            margin-top: 20px;
        }

        #deliveryImage {
            display: none;
            width: 550px; /* 조절 필요 */
            height: 400px; /* 조절 필요 */
        }
        #otherImage {
            display: none;
            width: 300px; /* 다른 이미지의 가로 크기 (조절 가능) */
            height: 400px; /* 다른 이미지의 세로 크기 (조절 가능) */
}
    </style>
</head>
<body>
<button id="startButton" onclick="startDelivery()">배달 시작</button>
<button id="completeButton" onclick="completeDelivery()" style="display:none;">배달 완료</button>
<p id="deliveryStatus">배달 대기 중</p>
<img id="deliveryImage" src="배달중.gif" alt="배달 중 이미지">
<img id="otherImage" src="배달완료.jpg" alt="다른 이미지">

<script>
    function startDelivery() {
        var startButton = document.getElementById('startButton');
        var completeButton = document.getElementById('completeButton');
        var deliveryStatusElement = document.getElementById('deliveryStatus');
        var deliveryImage = document.getElementById('deliveryImage');

        startButton.style.display = 'none';
        completeButton.style.display = 'inline-block';

        deliveryStatusElement.innerText = '배달 중입니다...';
        deliveryImage.style.display = 'inline-block';
    }

    function completeDelivery() {
        var completeButton = document.getElementById('completeButton');
        var deliveryStatusElement = document.getElementById('deliveryStatus');
        var deliveryImage = document.getElementById('deliveryImage');

        completeButton.style.display = 'none';
        deliveryStatusElement.innerText = '배달을 완료했습니다!';
        deliveryImage.src = "배달완료.jpg"; // 다른 이미지로 교체
        deliveryImage.style.width = '250px'; // 다른 이미지의 가로 크기 조절
        deliveryImage.style.height = '500px'; // 다른 이미지의 세로 크기 조절
    }
</script>
</body>
</html>

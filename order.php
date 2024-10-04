<?php
// 데이터베이스 연결
$servername = "localhost";
$username = "user";
$password = "12345";
$dbname = "sample";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// 메뉴 ID로 메뉴 이름을 가져오는 함수
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

// 메뉴 이름으로 이미지 경로를 가져오는 함수
function getMenuImagePath($conn, $menuName) {
    $imageQuery = "SELECT 이미지경로 FROM 메뉴 WHERE 메뉴이름 = '$menuName'";
    $imageResult = $conn->query($imageQuery);

    if ($imageResult->num_rows > 0) {
        $imageRow = $imageResult->fetch_assoc();
        return $imageRow['이미지경로'];
    } else {
        return ''; // 이미지를 찾을 수 없을 경우 빈 문자열 반환
    }
}
?>

<!DOCTYPE html>
<html lang="ko">
<head>
    <meta charset="utf-8">
    <title>주문 정보 및 결제</title>
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

        header {
            background-color: #333;
            color: white;
            text-align: center;
            padding: 10px;
        }

        section {
            background-color: white;
            padding: 20px;
            margin: 20px;
            border-radius: 8px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.1);
            width: 80%;
            max-width: 600px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center; /* 섹션 내 텍스트 가운데 정렬 */
        }

        h3 {
            color: #333;
            margin-bottom: 10px;
        }

        form {
            margin-top: 10px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        label {
            margin-right: 5px;
        }

        input[type="checkbox"] {
            margin-bottom: 10px;
        }

        img {
            max-width: 100%;
            height: auto;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .menu-image {
            max-width: 80%; /* 이미지 최대 너비 설정 */
            height: auto;
            margin-bottom: 10px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
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

<header>
    <h1>주문 정보 및 결제</h1>
</header>

<section>
    <h3>주문 정보</h3>
    <form action="pay.php" method="post">
        <?php
        // 주문 정보 출력
        foreach ($_POST as $key => $value) {
            if (strpos($key, '_qty') !== false && (int)$value > 0) {
                $menuID = str_replace('_qty', '', $key);
                $quantity = (int)$value;
                $menuName = getMenuName($conn, $menuID);
                $imagePath = getMenuImagePath($conn, $menuName);

                // 메뉴 ID와 수량을 hidden field로 추가
                echo '<input type="hidden" name="' . $menuID . '_id" value="' . $menuID . '">';
                echo '<input type="hidden" name="' . $menuID . '_qty" value="' . $quantity . '">';

                echo '<p>메뉴명: ' . $menuName . ', 수량: ' . $quantity . '</p>';
                
                if ($imagePath !== '') {
                    echo '<img src="' . $imagePath . '" alt="' . $menuName . '" class="menu-image">';
                }
            }
        }
        ?>
       

<section>
    <h3>쿠폰 선택 및 요청사항</h3>
    <form action="pay.php" method="post">
        <label for="delivery_discount">배달료 10% 할인 쿠폰</label>
        <input type="radio" id="delivery_discount" name="coupon" value="delivery_discount">
        
        <label for="2000_off">2000원 할인 쿠폰</label>
        <input type="radio" id="2000_off" name="coupon" value="2000_off">
        
        <label for="payment_method">결제수단</label>
        <select id="payment_method" name="payment_method">
            <option value="naver_pay">네이버페이</option>
            <option value="smile_pay">스마일페이</option>
            <option value="payco">페이코</option>
            <option value="toss_pay">토스페이</option>
            <option value="cash">현금결제</option>
            <option value="credit_card">신용카드</option>
            <option value="mobile_payment">휴대폰</option>
        </select>

        <!-- 요청사항을 위한 텍스트 입력란 추가 -->
        <label for="order_request">요청사항</label>
        <textarea id="order_request" name="order_request" rows="4" cols="50"></textarea>

        <input type="submit" style="margin-top: 30px;" value="결제하기">
    </form>
</section>

</body>
</html>

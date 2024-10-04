<?php
session_start(); // 세션 시작

$servername = "localhost";
$username = "user";
$password = "12345";
$dbname = "sample";

// 데이터베이스 연결
$conn = new mysqli($servername, $username, $password, $dbname);

// 연결 확인
if ($conn->connect_error) {
    die("연결 실패: " . $conn->connect_error);
}

// 가게 이름을 가져오기
$storeName = isset($_GET['store_id']) ? $_GET['store_id'] : '';

// 가게 이름으로 가게 ID 가져오기
$storeIDQuery = "SELECT 가게_ID FROM 가게 WHERE 가게이름 = '" . $storeName . "'";
$storeIDResult = $conn->query($storeIDQuery);

// 가게 ID가 존재하면 해당 가게의 메뉴 출력
if ($storeIDResult->num_rows > 0) {
    $storeIDRow = $storeIDResult->fetch_assoc();
    $storeID = $storeIDRow["가게_ID"];

    // 가게 정보를 가져오는 SQL 쿼리
    $storeQuery = "SELECT 가게이름 FROM 가게 WHERE 가게_ID = '" . $storeID . "'";
    $storeResult = $conn->query($storeQuery);

    // 선택된 가게의 메뉴 출력
    if ($storeResult->num_rows > 0) {
        // 가게 정보를 가져옴
        $storeRow = $storeResult->fetch_assoc();
        echo '<div style="text-align: center; margin-top:40px;">'; // 중앙 정렬 스타일 추가
        echo '<h1>' . $storeRow["가게이름"] . '</h1>';

        // 메뉴를 가져오는 SQL 쿼리
        $menuQuery = "SELECT * FROM 메뉴 WHERE 가게_ID = '$storeID'";
        $menuResult = $conn->query($menuQuery);

        echo '<form action="order.php" method="post">';
        echo '<ul style="list-style: none; padding: 0; margin-top: 180; text-align: center;">'; // 스타일 추가

        // 메뉴 출력
        if ($menuResult->num_rows > 0) {
            while ($menuRow = $menuResult->fetch_assoc()) {
                echo '<li style="margin-bottom: 10px; margin-right: 20px; display: inline-block; text-align: center; width: 25%;">'; // 스타일 추가
                // 이미지에 검정색 테두리 추가
                echo '<div style="max-width: 100%; height: auto; max-height: 400px; overflow: hidden; border: 4px solid #000; border-radius: 4px; margin: 0 auto;">';
                echo '<img src="' . $menuRow["이미지경로"] . '" alt="' . $menuRow["메뉴이름"] . '" style="width: 100%; height: 150px; object-fit: cover;">';
                echo '</div>';
                echo '<p style="margin-top: 10px; font-weight: bold;">' . $menuRow["메뉴이름"] . '</p>';
                echo $menuRow["가격"] . '원';
                echo '<label for="' . $menuRow["메뉴_ID"] . '_qty">   </label>';
                echo '<input type="number" id="' . $menuRow["메뉴_ID"] . '_qty" name="' . $menuRow["메뉴_ID"] . '_qty" value="0" min="0">';
                echo '</li>';
            }
        } else {
            echo "<p>해당하는 메뉴가 없습니다.</p>";
        }

        echo '</ul>';
        echo '<input type="hidden" name="store_id" value="' . $storeID . '">'; // 가게 ID를 숨겨 전송
        echo '<input type="submit" value="주문메뉴담기" style="background-color: #45a049;">'; // 수정된 부분
        echo '</form>';
        echo '</div>'; // 중앙 정렬 스타일 추가

        // 세션에 주문 정보 저장
        $_SESSION['store_info'] = array('가게_ID' => $storeID, '가게이름' => $storeRow["가게이름"]);
        $_SESSION['menu_info'] = array(); // 초기화
    } else {
        echo "<p>해당하는 가게가 없습니다.</p>";
    }
} else {
    echo "<p>가게 ID를 찾을 수 없습니다.</p>";
}

// 데이터베이스 연결 종료
$conn->close();
?>

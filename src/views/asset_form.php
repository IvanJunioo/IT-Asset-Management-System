<!DOCTYPE html>
<html lang="en">
    <?php include '../partials/head.php'?>
<body>
    <?php include '../partials/header.php'?>

    <main class="add-asset-form">
        <form action = '../includes/assets-inc.php' method="post">
            <label for="property-num"> Property Number: </label>
            <input type="text" id ="pnum" name="property-num" placeholder="Enter Property Number"></label>

            <label for="procurement-num"> Procurement Number: </label>
            <input type="text" id = "prnum" name="procurement-num" placeholder="Enter Procurement Number"></label>

            <label for="serial-num"> Serial Number: </label>
            <input type="text" id = "snum" name="serial-num" placeholder="Enter Serial Number"></label>

            <label for="purchase-date"> Purchase Date: </label>
            <input type="text" id = "pdate" name="purchase-date" placeholder="Enter Purchase Date"></label>

            <label for="price"> Price: </label>
            <input type="text" id = "price" name="price" placeholder="Enter Price"></label>

            <label for="specs"> Specifications: </label>
            <input type="text" id = "specs" name="specs" placeholder="Enter Specifications"></label>

            <label for="short-desc"> Short Description: </label>
            <input type="text" id ="desc" name="short-desc" placeholder="Enter Short Description"></label>

            <label for="remarks"> Remarks: </label>
            <input type="text" id = "remarks" name="remarks" placeholder="Enter Remarks"></label>

            <label for="img-url"> Img URL: </label>
            <input type="text" id = "img_url" name="img-url" placeholder="Enter Img URL"></label>

            <label for="act-log-form"> System Message: </label>
            <input type="text" id = "act-log" name="act-log-form" placeholder="Enter System Message"></label>

            <button id="reset-button">
                Reset
            </button>

            <button id="submit-button">
                Submit
            </button>  

        </form>
    </main>
    <?php include '../partials/footer.php'?>
</body>
</html>
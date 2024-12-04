<form id="documentForm" action="process_request.php" method="POST">
    <label><input type="checkbox" name="documents[]" value="COR"> Certificate of Registration (COR)</label><br>
    <label><input type="checkbox" name="documents[]" value="COG"> Certificate of Grades (COG)</label><br>
    <label><input type="checkbox" name="documents[]" value="CTC"> Certified True Copy (CTC)</label><br>
    <label><input type="checkbox" name="documents[]" value="TOR"> Transcript of Records (TOR)</label><br>
    <label><input type="checkbox" name="documents[]" value="Other"> Other: <input type="text" name="otherDocument"></label><br>
    <input type="hidden" name="studentNo" value="<?php echo $_SESSION['studentNo']; ?>">
    <button type="submit">Submit</button>
</form>

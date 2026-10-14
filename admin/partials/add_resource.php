<h1>Add Resource</h1>

<form id="add-resource-form" method="post" enctype="multipart/form-data">
    Title:
    <input type="text" name="resource_title" id="resource_title">
    <p style="color:red" id="titleerr"></p>

    <br><br>

    Content:
    <textarea name="resource_content" id="resource_content"></textarea>
    <p style="color:red" id="contenterr"></p>

    <br><br>

    Image:
    <input type="file" id="file" name="file">
    <p style="color:red" id="fileerr"></p>

    <img
        id="profileDisplay"
        src=""
        width="200"
        style="display:none;">

    <br><br>

    Capacity:
    <input type="number" name="resource_capacity" id="resource_capacity" value="1">
    <p style="color:red" id="capacityerr"></p>

    <br><br>

    <input type="submit" class="button button-primary">
</form>
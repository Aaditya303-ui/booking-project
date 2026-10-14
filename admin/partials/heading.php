<!-- Display the content -->
<div class="resource-wrap">
    <h1>Display Resources</h1>
    <h2>I will display all resources here</h2>

    <table class="resource-table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Title</th>
                <th>Capacity</th>
                <th>View</th>
                <th>Update</th>
                <th>Delete</th>
            </tr>
        </thead>
        <tbody id="mycontent"></tbody>
    </table>
</div>

<!-- View the content -->
<div id="resource-modal" class="booking-modal-overlay" style="display: none;">
    <div class="booking-modal-content">
        <span class="booking-modal-close">&times;</span>
        <h2 id="modal-title" style="margin-top: 0;"></h2>
        <div id="modal-content" style="margin: 15px 0;"></div>
        <p><strong>Capacity:</strong> <span id="modal-capacity"></span></p>
        <div id="modal-image-container"></div>
    </div>
</div>

<!-- Update the content -->
<div id="resource-edit-modal" class="booking-modal-overlay" style="display: none;">
    <div class="booking-modal-content">
        <h2 style="margin-top: 0;">Edit Resource</h2>
        
        <form id="resource-edit-form" enctype="multipart/form-data">
            <input type="hidden" id="edit-resource-id">
            
            <p>
                <label for="edit-resource-title"><strong>Title:</strong></label><br>
                <input type="text" id="edit-resource-title" class="regular-text" style="width: 100%; padding: 6px; margin-top: 5px;">
            </p>
            
            <p>
                <label for="edit-resource-content"><strong>Content:</strong></label><br>
                <textarea id="edit-resource-content" rows="4" style="width: 100%; padding: 6px; margin-top: 5px;"></textarea>
            </p>

            <p>
                <label><strong>Featured Image:</strong></label><br>
                <div id="edit-image-preview" style="margin: 8px 0;"></div>
                <img id="profileDisplay" src="" width="200" style="display:none;">
                <input type="file" id="edit-resource-file" accept="image/*" style="margin-top: 5px;">
            </p>
            
            <p>
                <label for="edit-resource-capacity"><strong>Capacity:</strong></label><br>
                <input type="number" id="edit-resource-capacity" class="regular-text" style="width: 100%; padding: 6px; margin-top: 5px;">
            </p>
            
            <p style="margin-top: 20px; text-align: right;">
                <button type="submit" class="button button-primary work">Save Changes</button>
            </p>
        </form>
    </div>
</div>
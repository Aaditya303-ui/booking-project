
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
            </tr>
        </thead>
        <tbody id="mycontent"></tbody>
    </table>
</div>

<div id="resource-modal" class="booking-modal-overlay" style="display: none;">
    <div class="booking-modal-content">
        <span class="booking-modal-close">&times;</span>
        <h2 id="modal-title" style="margin-top: 0;"></h2>
        <div id="modal-content" style="margin: 15px 0;"></div>
        <p><strong>Capacity:</strong> <span id="modal-capacity"></span></p>
        <div id="modal-image-container"></div>
    </div>
</div>
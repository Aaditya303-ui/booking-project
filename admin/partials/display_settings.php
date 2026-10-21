<div class="blackout-wrap">

    <div class="blackout-header">
        <div>
            <h1>Blackout Dates</h1>
        </div>

        <button type="button" id="add-blackout-btn" class="blackout-add-btn">
            + Add Date
        </button>
    </div>

    <div class="blackout-table-container">
        <table class="blackout-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Blackout Date</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody id="mycontent2">
                <!-- Data loaded through your existing AJAX function -->
            </tbody>
        </table>
    </div>

    <p id="blackout-empty-message" class="blackout-empty" style="display:none;">
        No blackout dates have been added yet.
    </p>

</div>


<div id="blackout-modal" style="display: none;">
    <form id="blackout-form">

        <h3>Add Blackout Date</h3>

        <label for="blackout_date">Date</label>
        <input type="date" id="blackout_date" required>

        <br><br>

        <label for="blackout_reason">Reason</label>
        <input type="text" id="blackout_reason"
               placeholder="Enter reason" required>

        <br><br>

        <button type="submit">Save Date</button>
        <button type="button" id="cancel-blackout">Cancel</button>

    </form>
</div>

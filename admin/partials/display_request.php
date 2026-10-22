<div class="request-wrap">
    <h1>Display Requests</h1>
    <h2>I will display all booking requests here</h2>

    <table class="request-table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Requested Resource</th>
                <th>Requester Name</th>
                <th>From</th>
                <th>To</th>
                <th>Status</th>
                <th>
                    Action
                </th>
            </tr>
        </thead>
        <tbody id="mycontent1"></tbody>
    </table>
</div>

<div id="view-request" style="display: none;">
    <div class="request-header">
        <h2 id="display_request_id">Booking Request #16</h2>
        <span>Booking Details</span>
    </div>
    <div class="request-details">
        <div class="detail-row">
            <label>Requested Resource</label>
            <p id="resource_name"></p>
        </div>
        <div class="detail-row">
            <label>Requester Name</label>
            <p id="requester_name"></p>
        </div>
        <div class="detail-row">
            <label>Requester Email</label>
            <p id="email"></p>
        </div>
        <div class="detail-row">
            <label>Date</label>
            <p id="date"></p>
        </div>
        <div class="detail-row">
            <label>From</label>
            <p id="from-date"></p>
        </div>
        <div class="detail-row">
            <label>To</label>
            <p id="to-date"></p>
        </div>
        <div class="detail-row">
            <label>Status</label>
            <select id="bookingStatus">
                <option value="pending">Pending</option>
                <option value="confirmed">Confirmed</option>
                <option value="cancelled">Cancelled</option>
            </select>
        </div>
    </div>
    <div class="request-actions">
        <button type="button" id="update-booking">
            Update Booking
        </button>
        <button type="button" id="close-request">
            Close
        </button>
    </div>
</div>
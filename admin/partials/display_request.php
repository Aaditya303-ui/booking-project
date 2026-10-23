<?php

global $wpdb;

$table_name = $wpdb->prefix . 'bookings';

$query = $wpdb->prepare("
                    SELECT 
                        b.id,
                        p.post_title,
                        b.requester_name,
                        b.start_time,
                        b.end_time,
                        b.status,
                        b.date,
                        b.created_at,
                        b.requester_email
                    FROM $table_name AS b
                    LEFT JOIN wp_posts AS p
                    ON b.resource_id = p.ID
        ");

$results = $wpdb->get_results($query);

$data = array();

foreach ($results as $row) {
    $data[] = array(
        'id'              => $row->id,
        'post_title'      => $row->post_title,
        'requester_name'  => $row->requester_name,
        'requester_email' => $row->requester_email,
        'date'            => $row->date,
        'start_time'      => $row->start_time,
        'end_time'        => $row->end_time,
        'status'          => $row->status,
        'created_at'      => $row->created_at
    );
}

?>
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
        <tbody id="mycontent1">
            <?php

            $i = 0;

            while ($i < count($data)) {
            ?>
                <tr>
                    <td><?php echo absint($data[$i]['id']); ?></td>
                    <td><?php echo esc_html($data[$i]['post_title']); ?></td>
                    <td><?php echo esc_html($data[$i]['requester_name']); ?></td>
                    <td><?php echo esc_html($data[$i]['start_time']); ?></td>
                    <td><?php echo esc_html($data[$i]['end_time']); ?></td>
                    <td><?php echo esc_html($data[$i]['end_time']); ?></td>
                    <td><?php echo esc_html($data[$i]['status']); ?></td>
                    <td>
                        <button id="view-btn" 
                        data-id=<?php echo $data[$i]['id']; ?>>
                            view
                        </button>
                    </td>
                </tr>  
            <?php 
            $i++;
            } ?>
        </tbody>
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
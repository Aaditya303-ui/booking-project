<?php

global $wpdb;

$table_name  = $wpdb->prefix . 'bookings';
$table_name1 = $wpdb->prefix . 'posts';

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

$results  = $wpdb->get_results($query);

$data  = array();
$data1 = array();

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
        'created_at'      => $row->created_at,
        'resource_id'     => $row->resource_id
    );
}
$query = $wpdb->prepare("SELECT * 
                        FROM wp_posts as p
                        WHERE p.post_type = 'resource'
                        and p.post_status = 'publish'
        ");
$results1 = $wpdb->get_results($query);

foreach ($results1 as $row) {
    $data1[] = array(
        'id'         => $row->ID,
        'post_title' => $row->post_title
    );
}

?>
<div class="request-wrap">
    <h1>Display Requests</h1>
    <h2>I will display all booking requests here</h2>
    <div class="booking-filters-bar">

        <input type="text" id="searchbooking" placeholder="Search by name, email..." />

        <select id="statusFilter">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="confirmed">Confirmed</option>
            <option value="expired">Expired</option>
            <option value="cancelled">Cancelled</option>
        </select>


        <select id="sortBy">
            <option value="">Select based on order</option>
            <option value="a-z">A-Z</option>
            <option value="z-a">Z-A</option>
            <option value="new">Newest First</option>
            <option value="old">Oldest First</option>
        </select>

        <select id="resources">
            <option value="">Show All</option>
            <?php
            $i = 0;
            while ($i < count($data1)) { ?>
                <option value="<?php echo $data1[$i]['id'] ?>">
                    <?php echo $data1[$i]['post_title']; ?>
                </option>
            <?php
                $i++;
            } ?>
        </select>
    </div>
    <table class="request-table">
        <thead>
            <tr>
                <th>Id</th>
                <th>Requested Resource</th>
                <th>Requester Name</th>
                <th>From</th>
                <th>To</th>
                <th>
                    Action
                </th>
                <th>
                    View
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
                    <td>
                        <?php
                        if ($data[$i]['status'] == "expired") { ?>
                            <select id="bookingStatus"
                                data-id="<?php echo $data[$i]['id'] ?>">
                                <option selected value="expired">Expired</option>
                            </select>
                        <?php
                        } else if ($data[$i]['status'] == "pending") {
                        ?>
                            <select
                                id="bookingStatus"
                                data-id="<?php echo $data[$i]['id'] ?>">
                                <option selected value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        <?php
                        } else if ($data[$i]['status'] == "cancelled") {
                        ?>
                            <select id="bookingStatus"
                                data-id="<?php echo $data[$i]['id'] ?>">
                                <option value="confirmed">Confirmed</option>
                                <option selected value="cancelled">Cancelled</option>
                            </select>
                        <?php
                        } elseif ($data[$i]['status'] == "confirmed") {
                        ?>
                            <select id="bookingStatus"
                                data-id="<?php echo $data[$i]['id'] ?>">
                                <option selected value="confirmed">Confirmed</option>
                                <option value="cancelled">Cancelled</option>
                            </select>
                        <?php
                        }
                        ?>
                    </td>
                    <td>
                        <button class="view-btn" 
                                data-id=<?php echo $data[$i]['id']; ?>
                                data-rid=<?php echo $data[$i]['resource_id']; ?>>
                            view
                        </button>
                    </td>
                    <div id="view-request" style="display: none;">
                        <div class="request-header">
                            <h2 id="display_request_id">Booking Request #16</h2>
                            <span>Booking Details</span>
                            <input type="hidden" id="resource_id">
                        </div>
                        <div class="request-details">
                            <div class="detail-row">
                                <label>Requested Resource</label>
                                <p id="resource_name"></p>
                            </div>
                            <div class="detail-row">
                                <label>Requester Name</label>
                                <input type="text" id="requester_name" />
                                <p style="color: red;" id="RequesternameErr"></p>
                            </div>
                            <div class="detail-row">
                                <label>Requester Email</label>
                                <input type="email" id="email" />
                                <p id="RemailErr" style="color:red;"></p>
                            </div>
                            <div class="detail-row">
                                <label>Date</label>
                                <input type="date" id="date">
                                <p id="RdateErr" style="color:red;"></p>
                            </div>
                            <div class="detail-row">
                                <label>From</label>
                                <input type="time" id="from-date" />
                                <p id="RfromErr" style="color:red;"></p>
                            </div>
                            <div class="detail-row">
                                <label>To</label>
                                <input type="time" id="to-date" />
                                <p id="RtoErr" style="color:red;"></p>
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
                </tr>
            <?php
                $i++;
            } ?>
        </tbody>
    </table>
</div>
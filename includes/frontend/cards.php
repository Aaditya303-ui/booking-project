<?php 

?>
<h2>Hello from display</h2>
<div id="content1" class="card-content" style="display: flex;">loading...</div>

<div id="booking-modal" class="booking-modal-overlay" style="display: none;">

    <div class="booking-modal-content">

        <span class="booking-modal-close">&times;</span>

        <!-- Resource information -->

        <div id="booking-resource-image"></div>

        <h2 id="booking-resource-title"></h2>

        <div id="booking-resource-content"></div>

        <p>
            <strong>Capacity:</strong>
            <span id="booking-resource-capacity"></span>
            <input type="hidden" id="resource_id">
        </p>

        <hr>

        <!-- Booking form -->

        <h3>Request Booking</h3>

        <form id="booking-form">

            <!-- We need to know which resource user selected -->
            <input
                type="hidden"
                id="booking_resource_id"
                name="resource_id"
            >

            <div class="booking-field">

                <label for="requester-name">
                    Your Name
                </label>

                <input
                    type="text"
                    id="requester_name"
                    name="requester_name"
                    required
                >

            </div>

            <div class="booking-field">

                <label for="requester-email">
                    Email
                </label>

                <input
                    type="email"
                    id="requester_email"
                    name="requester_email"
                    required
                >

            </div>

            <div class="booking-field">

                <label for="start-time">
                    Start Time
                </label>

                <input
                    type="datetime-local"
                    id="start_time"
                    name="start_time"
                    required
                >

            </div>

            <div class="booking-field">

                <label for="end-time">
                    End Time
                </label>

                <input
                    type="datetime-local"
                    id="end_time"
                    name="end_time"
                    required
                >

            </div>

            <button type="submit">
                Submit Booking
            </button>

            <div id="booking-message"></div>

        </form>

    </div>

</div>
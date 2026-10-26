<?php
$args = array(
    'post_type'      => 'resource',
    'post_status'    => 'publish',
    'posts_per_page' => -1,
);

$posts = get_posts($args);

$data = array();

foreach ($posts as $post) {
    $thumbnail_url = get_the_post_thumbnail_url($post->ID, 'full');
    $capacity      = get_post_meta($post->ID, '_capacity', true);
    $data[] = array(
        'id'         => $post->ID,
        'title'      => $post->post_title,
        'slug'       => $post->post_name,
        'thumbnail'  => $thumbnail_url,
        '_capacity'  => $capacity,
        'content'    => $post->post_content
    );
}
?>
<h2>Hello from display</h2>

<div style="display: flex;">
    <input type="text" id="searchRes" style="width: 150px;" placeholder="search">
    <select id="filter" style="width: 200px; margin-right:10px">
        <option value="">Select the order</option>
        <option value="a-z">a-z</option>
        <option value="z-a">z-a</option>
        <option value="new">Latest</option>
        <option value="old">Older</option>
    </select>
</div>
<br><br>

<div id="content1" class="card-content" style="display: flex; flex-wrap:wrap">
    <?php
    $i = 0;
    while ($i < count($data)) {

        $id            = absint($data[$i]['id']);
        $title         = esc_html($data[$i]['title']);
        $capacity      = absint($data[$i]['_capacity']);
        $content       = esc_html($data[$i]['content']);
        $excerpt       = esc_html(substr($content, 0, 30));
        $thumbnail_url = esc_html($data[$i]['thumbnail']);
    ?>
        <div class="resource-card" style="
                    width: 200px;
                    border: 1px solid #ddd;
                    border-radius: 10px;
                    overflow: hidden;
                    background: #fff;
                    box-shadow: 0 2px 6px rgba(0,0,0,0.1);
                    margin: 10px;
                ">

            <img
                style="
                            width: 100%;
                            height: 160px;
                            object-fit: cover;
                            display: block;
                        "
                class="images"
                src="<?php echo $thumbnail_url; ?>"
                alt="<?php echo $title; ?>">

            <div class="resource-card-content" style="padding: 15px;">

                <h3 style="
                            margin: 0 0 10px;
                            font-size: 18px;
                        "><?php echo $title; ?></h3>

                <p style="
                            color: #666;
                            font-size: 14px;
                            margin: 0 0 12px;
                        "><?php echo $excerpt; ?></p>

                <p style="margin: 0 0 15px;">
                    <strong>Capacity:</strong> <?php echo $capacity; ?> people
                </p>

                <button
                    class="view-resource-link"
                    data-id="<?php echo $id; ?>"
                    style="
                                width: 100%;
                                padding: 10px;
                                background: #2563eb;
                                color: white;
                                border: none;
                                border-radius: 6px;
                                cursor: pointer;
                            ">
                    View
                </button>

            </div>
        </div>
    <?php
        $i++;
    }
    ?>
</div>
<div id="ajax-slot-status" style="display: none; padding: 10px; margin-bottom: 15px; border-radius: 4px; font-size: 13px; font-weight: bold;"></div>
<div id="booking-modal" class="booking-modal-overlay" style="display: none;">

    <div class="booking-modal-content">

        <span class="booking-modal-close">&times;</span>

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
        <div id="time-occupied"></div>
        <form id="booking-form">
            <div id="ajax-slot-status1"></div>
            <input
                type="hidden"
                id="booking_resource_id"
                name="resource_id">

            <div class="booking-field">

                <label for="requester-name">
                    Your Name
                </label>

                <input
                    type="text"
                    id="requester_name"
                    name="requester_name"
                    required>
                <div id="nameerr"></div>
            </div>

            <div class="booking-field">

                <label for="requester-email">
                    Email
                </label>

                <input
                    type="email"
                    id="requester_email"
                    name="requester_email"
                    required>
                <div id="emailerr"></div>
            </div>

            <div class="booking-field">

                <label for="start-time">
                    Date
                </label>

                <input type="date" name="" id="date">
                <br><br>
                <p id="buisness-hours"></p>
                <br><br>
                <label>Start Time</label>
                <input type="time" name="" id="start_time">
                <br><br>
                <div id="end-time" style="display:none;">
                    <label>End Time</label>
                    <input type="time"
                        name=""
                        id="end_time">
                </div>
                <br><br>
            </div>

            <div class="booking-field">
            </div>

            <button id="btn1" type="submit">
                Submit Booking
            </button>

            <div id="booking-message"></div>

        </form>

    </div>

</div>
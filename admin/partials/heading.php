<!-- Display the content -->
<?php
// sql query array store

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
        <tbody id="mycontent">
            <?php
            $i = 0;

            while ($i < count($data)) {
            ?>
                <tr>
                    <td><?php echo $data[$i]['id'] ?></td>
                    <td><?php echo $data[$i]['title'] ?></td>
                    <td><?php echo $data[$i]['_capacity'] ?></td>
                    <td>
                        <button
                            class="view-resource-link"
                            data-id="<?php echo $data[$i]['id'] ?>">
                            view
                        </button>
                        <div
                            id="resource-modal-<?php echo $data[$i]['id']; ?>"
                            class="booking-modal-overlay"
                            style="display:none;">
                            <div class="booking-modal-content">
                                <span class="booking-modal-close">×</span>
                                <h2>
                                    <?php echo $data[$i]['title']; ?>
                                </h2>
                                <div>
                                    <?php echo $data[$i]['content']; ?>
                                </div>
                                <p>
                                    <strong>Capacity:</strong>
                                    <?php echo $data[$i]['_capacity']; ?>
                                </p>
                                <?php if (!empty($data[$i]['thumbnail'])) { ?>
                                    <img
                                        src="<?php echo $data[$i]['thumbnail']; ?>"
                                        alt="">
                                <?php } ?>
                            </div>
                        </div>
                    </td>
                    <td>
                        <button
                            class="update-resource-link"
                            data-id="<?php echo $data[$i]['id'] ?>">
                            update
                        </button>
                    </td>
                    <div id="resource-edit-modal-<?php echo $data[$i]['id'] ?>" class="booking-modal-overlay" style="display: none;">
                        <div class="booking-modal-content">
                            <h2 style="margin-top: 0;">Edit Resource</h2>

                            <form id="resource-edit-form" enctype="multipart/form-data">
                                <input type="hidden"
                                    id="edit-resource-id"
                                    value="<?php echo $data[$i]['id'] ?>">
                                <p>
                                    <label for="edit-resource-title"><strong>Title:</strong></label><br>

                                    <?php if (!empty($data[$i]['thumbnail'])) { ?>
                                        <input
                                            type="text"
                                            id="edit-resource-title"
                                            class="regular-text"
                                            value="<?php echo $data[$i]['title'] ?>"
                                            style="width: 100%; padding: 6px; margin-top: 5px;">
                                    <?php } ?>
                                <p style="color:red" id="titleerr"></p>
                                </p>
                                <p>
                                    <label for="edit-resource-content"><strong>Content:</strong></label><br>
                                    <textarea
                                        id="edit-resource-content"
                                        rows="4"
                                        style="width: 100%; padding: 6px; margin-top: 5px;"><?php echo esc_textarea($data[$i]['content']); ?></textarea>
                                <p style="color:red" id="contenterr"></p>
                                </p>

                                <p>
                                    <label><strong>Featured Image:</strong></label><br>
                                <div id="edit-image-preview" style="margin: 8px 0;"></div>
                                <input
                                    type="hidden"
                                    id="old-image-<?php echo $data[$i]['id']; ?>"
                                    value="<?php echo $data[$i]['thumbnail']; ?>">
                                <img
                                    id="profileDisplay-<?php echo $data[$i]['id']; ?>"
                                    src="<?php echo $data[$i]['thumbnail'] ?>"
                                    width="200">
                                <input type="file" id="edit-resource-file" accept="image/*" style="margin-top: 5px;">
                                </p>
                                <p>
                                    <label for="edit-resource-capacity"><strong>Capacity:</strong></label><br>
                                    <input
                                        type="number"
                                        id="edit-resource-capacity"
                                        class="regular-text"
                                        style="width: 100%; padding: 6px; margin-top: 5px;"
                                        value="<?php echo $data[$i]['_capacity']; ?>">
                                <p style="color:red" id="capacityerr"></p>
                                </p>

                                <p style="margin-top: 20px; text-align: right;">
                                    <button type="submit" class="button button-primary work">Save Changes</button>
                                </p>
                            </form>
                        </div>
                    </div>
                    <td>
                        <button
                            class="delete-resource"
                            data-id="<?php echo $data[$i]['id'] ?>">
                            Delete
                        </button>
                    </td>
                </tr>
            <?php
                $i++;
            } ?>
        </tbody>
    </table>
</div>


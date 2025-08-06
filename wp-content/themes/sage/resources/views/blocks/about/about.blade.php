@if( is_admin() && empty( $data ) && ( 1 != 1 ) ) <!-- Add condition if the data fetched is empty and it is admin/editor-->
    <img src="https://www.origamirisk.com/wp-content/uploads/2025/05/Missing-Headshot-Placeholder.jpg" alt="">
@else
    <section class="about" style="background-color:blueviolet">
        About Module for Testing.
    </section>
@endif

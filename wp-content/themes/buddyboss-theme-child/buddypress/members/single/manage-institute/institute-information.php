<div class="institute-information-container">
    <section class="institute-info">
        <h2>Institute Information</h2>
        <div class="logo-update">
            <div class="logo">
                <div class="logo-image"></div>
            </div>
            <div class="buttons">
                <button type="button" class="button-update">Update Logo</button>
                <button type="button" class="button-remove">Remove</button>
            </div>
            
        </div>
    </section>

    <section class="institute-name">
        <h2>Institute Name</h2>
        <input type="text" name="institute_name" class="input-institute-name"/>
    </section>

    <section class="facial-features">
        <h2>Facial Features</h2>
        <div class="facial-features"><?=do_shortcode("[gf_facial_features]");?></div>
        <div class="buttons">
            <button type="button" class="button-save">Save</button>
        </div>
    </section>
</div>

<div id="upload-insitute-logo-modal" class="modal" >
    <!-- Modal content -->
    <div class="modal-content">            
        <span class="close">
            <svg width="50" height="50" viewBox="0 0 50 50" fill="none" xmlns="http://www.w3.org/2000/svg">
                <circle cx="25" cy="25" r="25" fill="#98C03D"/>
                <rect width="24" height="24" transform="translate(13 13)" fill="#98C03D"/>
                <path d="M31 19L19 31" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
                <path d="M19 19L31 31" stroke="white" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
            </svg>
        </span>
        <div class="main-content">
            <h2>Upload Institute Logo</h2>
            <p>Please use a maximum width of 500px for better quality</p>
            <?=do_shortcode("[custom_gf_logo_upload]");?>
            <button type="button" class="button-submit-logo">Submit</button>
        </div>
    </div>

</div>
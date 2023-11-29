<button type="button" class="btn-back btn-back-to-import-student">
    <svg width="40" height="40" viewBox="0 0 40 40" fill="none" xmlns="http://www.w3.org/2000/svg">
        <circle cx="20" cy="20" r="19.5" stroke="#5D53C0"/>
        <path d="M23 26L17 20L23 14" stroke="#5D53C0" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/>
    </svg>
</button>

<h2>Student List Template</h2>

<div class="template-content">
    <?=get_field("student_list_template", get_the_id())?>
</div>
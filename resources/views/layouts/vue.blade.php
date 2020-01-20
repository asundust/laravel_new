@if (app()->isLocal())
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>
@else
    <script src="https://cdn.jsdelivr.net/npm/vue"></script>
@endif
<script src="https://cdn.jsdelivr.net/npm/axios@0.19.0/dist/axios.min.js"
        integrity="sha256-S1J4GVHHDMiirir9qsXWc8ZWw74PHHafpsHp5PXtjTs=" crossorigin="anonymous"></script>
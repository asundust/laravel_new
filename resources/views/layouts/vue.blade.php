@if (app()->isLocal())
  <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11/dist/vue.common.dev.js"
          integrity="sha256-soI/D3XnqcarOMK229d8GWs8P+gYViEsbWBeMaRoSPk=" crossorigin="anonymous"></script>
@else
  <script src="https://cdn.jsdelivr.net/npm/vue@2.6.11/dist/vue.min.js"
          integrity="sha256-ngFW3UnAN0Tnm76mDuu7uUtYEcG3G5H1+zioJw3t+68=" crossorigin="anonymous"></script>
@endif
<script src="https://cdn.jsdelivr.net/npm/axios@0.19.2/dist/axios.min.js"
        integrity="sha256-T/f7Sju1ZfNNfBh7skWn0idlCBcI3RwdLSS4/I7NQKQ=" crossorigin="anonymous"></script>
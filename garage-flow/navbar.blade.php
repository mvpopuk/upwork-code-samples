    <!-- Modal -->

    <div class="modal fade" id="selectCustomerModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content rounded">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLongTitle"><i class="icon-user"></i> Select Customer</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

              <div id="app">
                <auto-complete></auto-complete>
              </div>

          </div>


          <div class="modal-footer">
            <button type="button" class="btn btn-secondary rounded" data-dismiss="modal">Cancel</button>
          </div>
        </div>
      </div>
    </div>

    <!-- End of Modal -->



  <script type="text/javascript">
    $(document).ready(function() {
      $(".dropdown-toggle").dropdown();
    });
  </script>

  <script>

    Vue.component('autoComplete', {
      template:
              '<div>' +
              '<div class="input-group float-right">' +
              '<input type="text" ' +
              'id="instantSearch" ' +
              'v-model="instantSearch" ' +
              'v-on:keyup="getSearchData" ' +
              'class="form-control rounded-left border-right-0 font-italic" ' +
              'placeholder="Type in and search...">' +
              '<span class="input-group-addon bg-white border-left-0 rounded-right"><i class="fa fa-search"></i></span>' +
              '</div>' +
              '<div class="row col-12 pr-0 pl-0 ml-0">' +
              '<ul v-if="results.length" style="width: 100%;" class="list-group">' +
              '<li class="list-group-item" v-for="result in results">' +
              '@{{ result.id }} @{{ result.FirstName }} @{{ result.LastName }}' +
              '<div class="d-inline float-right m-1">' +
              '<a class="btn btn-primary btn-sm rounded" v-bind:href="`/jobs/${result.id}/create`">Select</a>'+
              '</li>' +
              '</div>' +
              '</ul>' +
              '</div>' +
              '</div>',

      data: function () {
        return {
          instantSearch: '',
          results: []
        }
      },

      methods: {
        getSearchData(){
          this.results = [];
          if(this.instantSearch.length > 0){
            axios.get('instantSearch',{params: {instantSearch: this.instantSearch}}).then(response => {
              this.results = response.data;
            });
          }
        }
      },
    });

    const app = new Vue({
      el: '#app'
    });

  </script>

  <ul class="nav navbar-nav ml-auto">

    <li class="nav-item d-md-down-none">
      <a class="nav-link" href="#"><i class="icon-bell"></i><span class="badge badge-pill badge-danger">5</span></a>
    </li>

    <li class="nav-item dropdown">
      <a class="nav-link dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false">
        @if (Auth::check())
          <img src="https://www.gravatar.com/avatar/{{md5(strtolower(trim(Auth::user()->email)))}}?s=160&d=retro" class="img-avatar" alt="{{ Auth::user()->email }}">
        @else
          <img src="{{ asset('img/avatars/6.jpg') }}" class="img-avatar" alt="admin@bootstrapmaster.com">
        @endif
      </a>
      <div class="dropdown-menu dropdown-menu-right">
        <div class="dropdown-header text-center">
          <strong>{{ auth()->user()->name }}'s Account</strong>
        </div>
        <a class="dropdown-item" href="#"><i class="fa fa-bell-o"></i> Updates<span class="badge badge-info">42</span></a>
        <a class="dropdown-item" href="#"><i class="fa fa-envelope-o"></i> Messages<span class="badge badge-success">42</span></a>
        <a class="dropdown-item" href="#"><i class="fa fa-tasks"></i> Tasks<span class="badge badge-danger">42</span></a>
        <a class="dropdown-item" href="#"><i class="fa fa-comments"></i> Comments<span class="badge badge-warning">42</span></a>
        <div class="dropdown-header text-center">
          <strong>Settings</strong>
        </div>
        <a class="dropdown-item" href="#"><i class="fa fa-user"></i> Profile</a>
        <a class="dropdown-item" href="#"><i class="fa fa-wrench"></i> Settings</a>
        <a class="dropdown-item" href="#"><i class="fa fa-usd"></i> Payments<span class="badge badge-secondary">42</span></a>
        <a class="dropdown-item" href="#"><i class="fa fa-file"></i> Projects<span class="badge badge-primary">42</span></a>
        <div class="divider"></div>
        <a class="dropdown-item" href="#"><i class="fa fa-shield"></i> Lock Account</a>

        <a class="dropdown-item" href="{{ route('logout') }}"
          onclick="event.preventDefault();
          document.getElementById('logout-form').submit();">
          <i class="fa fa-lock"></i> Logout </a>
      </div>
    </li>
  </ul>

  <button class="navbar-toggler aside-menu-toggler" type="button">
    <span class="navbar-toggler-icon"></span>
  </button>

 <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
  {{ csrf_field() }}
</form>
</header>


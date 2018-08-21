@extends('layouts.blank')

@section('content')
    var header = {
        Config: {
            DevcoHost: 'https://devco.ohiohome.org',
            AllitaHost: 'https://pcinspectdev.ohiohome.org',
            Logo: 'https://devco.ohiohome.org/AuthorityOnlineALT/images/Logo.jpg',
            LogoAlt: 'Site Logo',
            Css: 'https://devco.ohiohome.org/AuthorityOnlineALT/universal-header.css',
            LeftItems: '',
            RightItems: '',
            UserID: '1',
            UserInitials: 'AA',
            UserName: 'AmeliaAtchinson (OSM Test)',
            UserToken: 'eHdJV2E3dzhnUXpsc3ArcU9uT3JFeXFEREtPQ1kxemcrT29teWlZMEdlTT06OWExNGZlODEtYzlhYi00MTA1LWE0NmUtY2UyMDY0ZGU2ZGJiOjYzNjY4NzM3Mzk4MDg4NTk4Ng==',
        },
        Init: function() {
            this._OverlayConfigValues();
            this._Render();
            this._AttachEvents();
        },
        _OverlayConfigValues: function() {
            if(typeof _uh !== 'undefined') {
                var index;
                for (index = 0; index < _uh.length; ++index) {
                    this.Config[_uh[index][0]] = _uh[index][1];
                }
            }
        },
        _RenderHtml: function() {
            var html = "";
            html = "" +
                "<div id='apcsv-ul-bar'>" +
                "  <div id='apcsv-logo'><img src='"+this.Config.Logo+"' alt='"+this.Config.LogoAlt+"'></div>" +
                "  <div id='apcsv-list-left'>"+this.Config.LeftItems+"</div>" +
                "  <div id='apcsv-avatar' title='"+this.Config.UserName+"'>"+this.Config.UserInitials+"</div>" +
                "  <div id='apcsv-menu-icon' class='hvr-grow'><a id='apcsv-toggle' class='pcsv-toggle' href='#apcsv-menu-items'>APPS</a>" +
                "    <div id='apcsv-menu-items' class='hidden'>" +
                "      <div class='apcsv-menu-item'> <a href='"+this.Config.DevcoHost+"/unified_login?user_id="+this.Config.UserID+"&token="+this.Config.UserToken+"'>DEV|CO Compliance</a></div>" +
                "      <div class='apcsv-menu-item'> <a href='"+this.Config.AllitaHost+"/unified_login?user_id="+this.Config.UserID+"&token="+this.Config.UserToken+"'>DEV|CO Inspection</a></div>" +
                "    </div>" +
                "  </div>" +
                "  <div id='apcsv-list-right'>"+this.Config.RightItems+"</div>" +
                "</div>";
            return html;
        },
        _RenderCss: function() {
            return '<link rel="stylesheet" href="'+this.Config.Css+'">';
        },
        _Render: function() {
            var rendered = '';
            rendered += this._RenderHtml();
            rendered += this._RenderCss();
            rendered = '<div id="ohfa-universal-header">' +rendered + '</div>';
            document.body.innerHTML = rendered + document.body.innerHTML;
        },
        _AttachEvents: function() {
            document.getElementById("apcsv-toggle").addEventListener("click", function( event ) {
                header.ToggleMenu();
            }, false);
        },
        ToggleMenu: function() {
            var menu = document.getElementById("apcsv-menu-items");
            if ( menu.classList.contains('hidden') ) {
                menu.classList.remove('hidden');
            } else {
                menu.classList.add('hidden');
            }
        }
    };

    window.onload = function() {
        header.Init();
    };
@endsection

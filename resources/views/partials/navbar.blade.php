<nav class="navbar">
      <a href="{{ route('home') }}" class="navbar-logo"
        ><img src="{{ asset('images/logo-alharus.png') }}" />MDT Al-Harus</a
      >

      <div class="navbar-nav">
        <!-- HOME -->
        <a href="{{ route('home') }}">
            <i class="bi bi-house"></i> Home
        </a>

        <!-- DROPDOWN PROFIL -->
        <div class="dropdown">
            <a href="#" class="dropbtn"><i class="bi bi-person-circle"></i> Profil</a>
            <div class="dropdown-content">
                <a href="{{ route('profil.madrasah') }}">
                    <i class="bi bi-building"></i> Madrasah
                </a>
                <a href="{{ route('profil.guru') }}">
                    <i class="bi bi-people"></i> Guru
                </a>
                <div class="dropdown-divider"></div>
                <a href="{{ route('profil.isma') }}">
                    <i class="bi bi-person-badge"></i> ISMA
                </a>
            </div>
        </div>
         <a href="{{ route('program-unggulan.index') }}">
            <i class="bi bi-star"></i> Program Unggulan
        </a>
        <a href="{{ route('kegiatan.index') }}">
            <i class="bi bi-calendar-event"></i> Kegiatan
        </a>
        <a href="{{ route('fasilitas.index') }}">
            <i class="bi bi-building"></i> Fasilitas
        </a>
        <a href="{{ route('infak.public.index') }}">
            <i class="bi bi-cash-stack"></i> Infak
        </a>
      </div>

      <div class="navbar-extra">
        <img src="{{ asset('images/logo-alharus.png') }}" />
        <a href="#" id="menu"><i data-feather="menu"></i></a>
      </div>
    </nav>
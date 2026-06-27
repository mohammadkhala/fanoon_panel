<footer class="footer">
    <div class="row justify-content-between align-items-center gy-2">
        <div class="col-lg-4">
            <p class="text-center text-lg-left mb-0">
                <a href="http://baitpait.com/" target="_blank" rel="noopener noreferrer" class="text-body">تطوير وبرمجة بيت البرمجيات وتكنولوجيا المعلومات</a>
            </p>
        </div>
        <div class="col-lg-8">
            <div class="d-flex justify-content-center justify-content-lg-end">
                <ul class="list-inline-menu justify-content-center">
                    <li>
                        <a href="{{route('branch.settings')}}">
                            {{translate('profile')}}
                            <i class="tio-user"></i>
                        </a>
                    </li>

                    <li>
                        <a href="{{route('branch.dashboard')}}">
                            <span>{{translate('Home')}}</span>
                            <i class="tio-home-outlined"></i>
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</footer>

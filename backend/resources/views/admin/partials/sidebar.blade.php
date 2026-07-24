@php
    $user = $user ?? auth()->user();
    $role = $user?->role ?? 'READER';
    $isAdmin = $role === 'ADMIN';
    $isEditor = in_array($role, ['ADMIN', 'EDITOR'], true);
    $isStaff = in_array($role, ['ADMIN', 'EDITOR', 'AUTHOR'], true);

    $groups = [
        'Overview' => [
            ['admin.dashboard', 'Dashboard', 'ड्यासबोर्ड', $isStaff],
        ],
        'Content' => [
            ['admin.articles.index', 'Articles', 'लेखहरू', $isStaff],
            ['admin.categories.index', 'Categories', 'वर्गहरू', $isEditor],
            ['admin.tags.index', 'Tags', 'ट्यागहरू', $isEditor],
            ['admin.comments.index', 'Comments', 'टिप्पणीहरू', $isEditor],
            ['admin.breaking-news.index', 'Breaking News', 'ब्रेकिङ न्युज', $isEditor],
            ['admin.web-stories.index', 'Web Stories', 'वेब स्टोरी', $isEditor],
            ['admin.panchang.index', 'Panchang', 'पञ्चाङ्ग', $isEditor],
        ],
        'Media' => [
            ['admin.media.index', 'Media Library', 'मिडिया', $isStaff],
            ['admin.galleries.index', 'Galleries', 'ग्यालेरी', $isEditor],
            ['admin.gallery-images.index', 'Gallery Images', 'ग्यालेरी छविहरू', $isEditor],
            ['admin.reels.index', 'Reels', 'रिल्स', $isEditor],
        ],
        'Sports' => [
            ['admin.sports.index', 'Tournaments', 'प्रतियोगिता', $isEditor],
            ['admin.teams.index', 'Teams', 'टोली', $isEditor],
            ['admin.matches.index', 'Matches', 'म्याच', $isEditor],
        ],
        'Finance' => [
            ['admin.finance.gold-silver', 'Gold-Silver', 'सुन-चाँदी', $isEditor],
            ['admin.finance.forex', 'Forex Rates', 'विनिमय दर', $isEditor],
        ],
        'Reference' => [
            ['admin.rashifal.index', 'Rashifal', 'राशिफल', $isEditor],
            ['admin.holidays.index', 'Holidays', 'बिदाहरू', $isEditor],
            ['admin.quick-links.index', 'Quick Links', 'द्रुत लिंक', $isEditor],
        ],
        'Insights' => [
            ['admin.analytics.index', 'Analytics', 'विश्लेषण', $isEditor],
            ['admin.bookmarks.index', 'Bookmarks', 'बुकमार्क', $isEditor],
        ],
        'Administration' => [
            ['admin.users.index', 'Users', 'प्रयोगकर्ता', $isAdmin],
            ['admin.ads.index', 'Ads', 'विज्ञापन', $isAdmin],
            ['admin.newsletter.index', 'Newsletter', 'न्यूजलेटर', $isAdmin],
            ['admin.settings.index', 'Settings', 'सेटिङ', $isAdmin],
            ['admin.audit-log.index', 'Audit Log', 'अडिट लग', $isAdmin],
            ['admin.profile.show', 'My Profile', 'प्रोफाइल', $isStaff],
        ],
    ];
@endphp

@foreach ($groups as $title => $items)
    @php
        $visible = array_filter($items, fn ($i) => $i[3]);
        if (empty($visible)) continue;
    @endphp
    <div class="nav-section">
        <div class="nav-group-title">{{ $title }}</div>
        @foreach ($visible as $item)
            @php [$route, $en, $ne, ] = $item; @endphp
            @php
                $isActive = request()->routeIs($route)
                    || request()->routeIs(str_replace('.index', '*', $route));
            @endphp
            <a href="{{ route($route) }}" class="nav-item {{ $isActive ? 'active' : '' }}">
                <span class="dot"></span>
                <span>{{ $ne }}</span>
                <small style="color:var(--muted-foreground);margin-left:auto;font-size:11px;">{{ $en }}</small>
            </a>
        @endforeach
    </div>
@endforeach
<div class="language-switcher">
    <div class="language-buttons">
        <a href="{{ route('installer.language', 'en') }}"
            class="language-btn {{ app()->getLocale() === 'en' ? 'active' : '' }}">
            <img src="{{ asset('image/flag/us.svg') }}"
                alt="EN" class="flag-icon" />
            <span>EN</span>
        </a>
        <a href="{{ route('installer.language', 'id') }}"
            class="language-btn {{ app()->getLocale() === 'id' ? 'active' : '' }}">
            <img src="{{ asset('image/flag/id.svg') }}"
                alt="ID" class="flag-icon" />
            <span>ID</span>
        </a>
    </div>
</div>

<style>
    .language-switcher {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 1050;
    }

    .language-buttons {
        display: flex;
        gap: 8px;
        background: white;
        border-radius: 8px;
        padding: 4px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        border: 1px solid #dee2e6;
    }

    .language-btn {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 8px 12px;
        text-decoration: none;
        color: #6c757d;
        border-radius: 6px;
        transition: all 0.2s ease;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        min-width: 60px;
        justify-content: center;
    }

    .language-btn:hover {
        background: #f8f9fa;
        color: #495057;
        text-decoration: none;
        transform: translateY(-1px);
    }

    .language-btn.active {
        background: #344D95;
        color: white;
        box-shadow: 0 2px 8px rgba(52, 77, 149, 0.3);
    }

    .language-btn.active:hover {
        background: #2d4085;
        color: white;
    }

    .flag-icon {
        width: 18px;
        height: 13px;
        border-radius: 2px;
        object-fit: cover;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2);
    }

    @media (max-width: 768px) {
        .language-switcher {
            position: relative;
            top: auto;
            right: auto;
            margin-bottom: 1rem;
            text-align: center;
        }

        .language-buttons {
            justify-content: center;
            display: inline-flex;
        }

        .language-btn {
            font-size: 0.8rem;
            padding: 6px 10px;
            min-width: 50px;
        }

        .flag-icon {
            width: 16px;
            height: 12px;
        }
    }

    /* Hover effects */
    .language-btn {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }

    .language-btn:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
    }

    .language-btn:active {
        transform: translateY(0);
        transition: transform 0.1s ease;
    }

    /* Accessibility */
    .language-btn:focus {
        outline: 2px solid #344D95;
        outline-offset: 2px;
    }

    /* Animation for active state change */
    .language-btn.active {
        animation: activeLanguage 0.3s ease;
    }

    @keyframes activeLanguage {
        0% {
            transform: scale(1);
        }

        50% {
            transform: scale(1.05);
        }

        100% {
            transform: scale(1);
        }
    }
</style>
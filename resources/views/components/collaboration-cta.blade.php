{{-- WORKSITEM_COLLABORATION_CTA_V1 --}}
<section class="ws-collab-section" aria-labelledby="wsCollabTitle">
    <style>
        .ws-collab-section{
            padding:28px 20px;
        }

        .ws-collab-box{
            max-width:1180px;
            margin:0 auto;
            padding:26px;
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:24px;
            flex-wrap:wrap;

            background:var(--ws-surface, var(--surface, #fff));
            border:1px solid var(--ws-border, var(--line, #e5e7eb));
            border-radius:18px;
        }

        .ws-collab-copy{
            max-width:760px;
        }

        .ws-collab-kicker{
            margin-bottom:7px;
            color:#a66b00;
            font-size:11px;
            font-weight:900;
            letter-spacing:.55px;
            text-transform:uppercase;
        }

        .ws-collab-box h2{
            margin:0 0 8px;
            color:var(--ws-text, var(--text, #111827));
            font-size:21px;
            line-height:1.3;
        }

        .ws-collab-box p{
            margin:0;
            color:var(--ws-muted, var(--muted, #6b7280));
            font-size:14px;
            line-height:1.65;
        }

        .ws-collab-topics{
            margin-top:9px !important;
            font-weight:700;
        }

        .ws-collab-btn{
            min-height:48px;
            padding:0 19px;
            display:inline-flex;
            align-items:center;
            justify-content:center;

            border-radius:11px;
            background:#ffb800;
            color:#111827 !important;
            font-weight:900;
            text-decoration:none !important;
            white-space:nowrap;
        }

        .ws-collab-btn:hover{
            background:#e9a800;
        }

        @media(max-width:640px){
            .ws-collab-section{
                padding:22px 16px;
            }

            .ws-collab-box{
                padding:21px;
            }

            .ws-collab-btn{
                width:100%;
            }
        }

/* WORKSITEM COLLAB TYPOGRAPHY V2 */
#wsCollabTitle{
    font-weight:800!important;
    letter-spacing:0!important;
    word-spacing:.06em!important;
    line-height:1.22!important;
}

</style>

    <div class="ws-collab-box">

        <div class="ws-collab-copy">
            <div class="ws-collab-kicker">
                İş Birliği & Öneriler
            </div>

            <h2 id="wsCollabTitle">
                WORKSİTEM ekosisteminde yer almak ister misiniz?
            </h2>

            <p>
                Yapı sektörüne ürün, hizmet, veri veya teknoloji sağlayan firmalarla
                iş birliklerine açığız. Yeni araç fikirlerinizi ve sektör önerilerinizi
                de değerlendirmek istiyoruz.
            </p>

            <p class="ws-collab-topics">
                Tedarikçi · Sponsorluk · Veri Kaynağı · Teknoloji İş Birliği · Yeni Araç Önerisi
            </p>
        </div>

        <a
            class="ws-collab-btn"
            href="mailto:info@worksitem.com?subject=WORKSITEM%20İş%20Birliği%20ve%20Öneri"
        >
            info@worksitem.com →
        </a>

    </div>
</section>

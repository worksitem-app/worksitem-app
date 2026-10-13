{{-- Hazır beton tedarikçi başvurusu (eski beton sayfasından taşındı) --}}
<style>
.supplier-slot{max-width:760px;margin:24px auto 0;padding:18px;text-align:left;border:1px dashed var(--line);border-radius:14px;background:var(--white)}
.supplier-slot small{font-weight:900;letter-spacing:.08em;color:var(--muted)}
.supplier-slot strong{display:block;margin:7px 0;font-size:17px}
.supplier-slot p{margin:0 0 12px;color:var(--muted);font-size:13px}
.supplier-slot a{font-weight:900;text-decoration:underline}
.supplier-slot .form-group{margin-bottom:12px}
.supplier-slot label{display:block;font-size:13px;font-weight:700;margin-bottom:6px}
</style>
<div class="supplier-slot">
            <small>TEDARİKÇİ ALANI</small>
            <strong>Türkiye genelinde hazır beton tedarik ediyor musunuz?</strong>
            <p>
                Her sınıf hazır beton, grobeton, pompalı veya pompasız ve
                proje bazlı beton sipariş taleplerini WORKSİTEM kullanıcılarından alın.
            </p>
            <!-- BETON_SUPPLIER_APPLICATION_V1 -->

<details
    id="supplierApplication"
    style="
        margin-top:16px;
        border-top:1px solid var(--line);
        padding-top:14px;
    "
>
    <summary
        style="
            cursor:pointer;
            font-weight:900;
            text-decoration:underline;
            list-style:none;
        "
    >
        Bu alanda yer alın →
    </summary>

    <div style="padding-top:18px;">

        <h4 style="margin:0 0 6px;">
            Hazır Beton Tedarikçi / İş Ortaklığı Başvurusu
        </h4>

        <p style="margin:0 0 18px;color:var(--muted);font-size:13px;line-height:1.6;">
            Hizmet verdiğiniz bölgeleri ve beton tedarik kapasitenizi iletin.
            Uygun taleplerde WORKSİTEM üzerinden sizinle iletişime geçelim.
        </p>

        <form action="{{ route('lead.store') }}" method="POST">
            @csrf

            <input
                type="hidden"
                name="source"
                value="worksitem_beton_tedarikci_basvuru"
            >

            <input
                type="hidden"
                name="source_tool"
                value="beton-hesaplayici"
            >

            <input
                type="hidden"
                name="project_type"
                value="Hazır Beton Tedarikçi / İş Ortaklığı"
            >

            <input
                type="hidden"
                name="calculated_service"
                value="Hazır Beton Tedarikçi Başvurusu"
            >

            <div class="form-group">
                <label for="supplierName">
                    Firma / Yetkili Adı
                </label>

                <input
                    id="supplierName"
                    name="name"
                    class="form-control"
                    required
                    maxlength="150"
                    placeholder="Firma adı veya yetkili kişi"
                >
            </div>

            <div class="form-group">
                <label for="supplierPhone">
                    Telefon
                </label>

                <input
                    id="supplierPhone"
                    name="phone"
                    type="tel"
                    class="form-control"
                    required
                    inputmode="tel"
                    placeholder="532 123 45 67"
                >
            </div>

            <div class="form-group">
                <label for="supplierLocation">
                    Hizmet Verdiğiniz İl / İlçeler
                </label>

                <input
                    id="supplierLocation"
                    name="location"
                    class="form-control"
                    required
                    maxlength="255"
                    placeholder="Örn. Adana, Mersin, Tarsus"
                >
            </div>

            <div class="form-group">
                <label for="supplierNotes">
                    Tedarik Bilgileri
                </label>

                <textarea
                    id="supplierNotes"
                    name="notes"
                    class="form-control"
                    maxlength="3000"
                    style="min-height:110px;height:auto;"
                    placeholder="Beton sınıfları, santral/bölgeler, mobil pompa hizmeti, minimum sipariş veya diğer bilgiler"
                ></textarea>
            </div>

            @include('components.supplier-consent-fields')

            <button
                type="submit"
                class="btn-submit"
                style="margin-top:16px;"
            >
                Tedarikçi başvurusunu gönder →
            </button>

            <p style="
                margin:14px 0 0;
                color:var(--muted);
                font-size:12px;
                line-height:1.5;
            ">
                Dilerseniz
                <a
                    href="mailto:info@worksitem.com?subject=WORKSITEM%20Tedarikçi%20Başvurusu"
                    style="font-weight:800;"
                >
                    info@worksitem.com
                </a>
                adresinden de ulaşabilirsiniz.
            </p>

        </form>

    </div>
</details>
        </div>

#!/usr/bin/env python3
"""Sisipkan sembilan diagram SKPL ke file draw.io TUMBUH UMKM."""

from pathlib import Path
import xml.etree.ElementTree as ET

ROOT = Path(__file__).resolve().parents[1]
DRAWIO = ROOT / "docs" / "PBL TUMBUH.drawio.xml"


def esc(text):
    return (
        str(text)
        .replace("&", "&amp;")
        .replace("<", "&lt;")
        .replace(">", "&gt;")
        .replace('"', "&quot;")
    )


class Diagram:
    def __init__(self, name, diagram_id, page_width, page_height):
        self.name = name
        self.diagram_id = diagram_id
        self.page_width = page_width
        self.page_height = page_height
        self.parts = []
        self.seq = 1
        self.boxes = {}

    def nid(self, prefix):
        self.seq += 1
        return f"{prefix}-{self.seq}"

    def node(self, cid, value, style, x, y, w, h):
        self.boxes[cid] = (x, y, w, h)
        self.parts.append(
            f'<mxCell id="{cid}" value="{esc(value)}" style="{style}" vertex="1" parent="1">'
            f'<mxGeometry x="{x}" y="{y}" width="{w}" height="{h}" as="geometry"/>'
            f"</mxCell>"
        )
        return cid

    def edge(self, source, target, label="", style=None, points=None, exit_xy=None, entry_xy=None):
        eid = self.nid("e")
        style = style or "edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;jettySize=auto;orthogonalLoop=1;strokeColor=#334155;fontSize=11;endArrow=block;endFill=1;"
        if exit_xy:
            style += f"exitX={exit_xy[0]};exitY={exit_xy[1]};exitDx=0;exitDy=0;"
        if entry_xy:
            style += f"entryX={entry_xy[0]};entryY={entry_xy[1]};entryDx=0;entryDy=0;"
        points_xml = ""
        if points:
            inner = "".join(f'<mxPoint x="{x}" y="{y}"/>' for x, y in points)
            points_xml = f"<Array as=\"points\">{inner}</Array>"
        self.parts.append(
            f'<mxCell id="{eid}" style="{style}" edge="1" parent="1" source="{source}" target="{target}">'
            f'<mxGeometry relative="1" as="geometry">{points_xml}</mxGeometry>'
            f"</mxCell>"
        )
        if label:
            lid = self.nid("l")
            self.parts.append(
                f'<mxCell id="{lid}" value="{esc(label)}" style="edgeLabel;html=1;align=center;verticalAlign=middle;resizable=0;points=[];fontSize=11;fontStyle=1;" vertex="1" connectable="0" parent="{eid}">'
                f'<mxGeometry relative="1" as="geometry"><mxPoint as="offset"/></mxGeometry>'
                f"</mxCell>"
            )
        return eid

    def title(self, text, x=24, y=12, w=900):
        self.node(
            self.nid("title"),
            text,
            "text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=middle;fontSize=18;fontStyle=1;fontColor=#0f172a;",
            x,
            y,
            w,
            32,
        )

    def xml(self):
        body = "\n        ".join(self.parts)
        return f"""  <diagram id="{self.diagram_id}" name="{esc(self.name)}">
    <mxGraphModel dx="1400" dy="900" grid="1" gridSize="10" guides="1" tooltips="1" connect="1" arrows="1" fold="1" page="1" pageScale="1" pageWidth="{self.page_width}" pageHeight="{self.page_height}" math="0" shadow="0">
      <root>
        <mxCell id="0"/>
        <mxCell id="1" parent="0"/>
        {body}
      </root>
    </mxGraphModel>
  </diagram>"""


def box_style(stroke, fill="#ffffff"):
    return (
        "rounded=1;whiteSpace=wrap;html=1;arcSize=14;fillColor="
        + fill
        + ";strokeColor="
        + stroke
        + ";fontSize=12;fontColor=#0f172a;align=center;verticalAlign=middle;spacing=4;"
    )


ACTION = box_style("#334155", "#ffffff")
SYSTEM = box_style("#5b21b6", "#f5f3ff")
DEC = "rhombus;whiteSpace=wrap;html=1;fillColor=#fef3c7;strokeColor=#a16207;fontSize=12;fontColor=#0f172a;align=center;verticalAlign=middle;spacing=2;"
START = "ellipse;html=1;aspect=fixed;fillColor=#111827;strokeColor=#111827;fontColor=#111827;verticalLabelPosition=bottom;verticalAlign=top;fontStyle=1;fontSize=11;"
END = "ellipse;html=1;aspect=fixed;fillColor=#ffffff;strokeColor=#111827;strokeWidth=3;fontColor=#111827;verticalLabelPosition=bottom;verticalAlign=top;fontStyle=1;fontSize=11;"
NOTE = "text;html=1;strokeColor=none;fillColor=none;align=left;verticalAlign=top;fontSize=11;fontColor=#475569;"
ARROW = "edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;jettySize=auto;orthogonalLoop=1;strokeColor=#334155;fontSize=11;endArrow=block;endFill=1;"
DASHED = ARROW + "dashed=1;"


def swimlane():
    d = Diagram("3.1 Swimlane Proses Bisnis", "skpl-31", 2000, 1120)
    d.title("Gambar 3.1 Swimlane Diagram Proses Bisnis TUMBUH UMKM", w=1100)
    lanes = [
        ("Pemilik UMKM", 56, 230, "#e0f2fe", "#0369a1"),
        ("Petugas Desa", 296, 270, "#dcfce7", "#166534"),
        ("Kepala Desa", 576, 180, "#fef9c3", "#a16207"),
        ("Sistem", 766, 240, "#ede9fe", "#5b21b6"),
    ]
    for name, y, h, fill, stroke in lanes:
        d.node(
            d.nid("lane"),
            "",
            f"rounded=0;whiteSpace=wrap;html=1;fillColor={fill};strokeColor={stroke};",
            24,
            y,
            1940,
            h,
        )
        d.node(
            d.nid("lane-label"),
            name,
            f"text;html=1;strokeColor=none;fillColor=none;align=center;verticalAlign=middle;fontSize=14;fontStyle=1;fontColor={stroke};horizontal=0;",
            28,
            y,
            150,
            h,
        )

    def act(cid, text, x, y, stroke, w=176, h=64):
        return d.node(cid, text, box_style(stroke), x, y, w, h)

    start = d.node("start", "Mulai", START, 210, 128, 34, 34)
    p1 = act("p1", "1. Registrasi", 270, 114, "#0369a1")
    p2 = act("p2", "2. Isi profil dan produk", 470, 114, "#0369a1", 190)
    p3 = act("p3", "3. Isi kuesioner", 690, 114, "#0369a1")
    p4 = act("p4", "8. Lihat rekomendasi", 1140, 114, "#0369a1")
    p5 = act("p5", "12. Lihat riwayat pembinaan", 1540, 114, "#0369a1", 200)
    end = d.node("end-ok", "Selesai", END, 1800, 128, 34, 34)

    t1 = act("t1", "2b. Pendataan UMKM tanpa akun", 470, 360, "#166534", 200)
    t2 = act("t2", "5. Verifikasi data", 900, 340, "#166534")
    t3 = act("t3", "6. Kelola soal dan program", 900, 450, "#166534", 200)
    t4 = act("t4", "9. Ajukan tindak lanjut", 1320, 360, "#166534", 190)
    t5 = act("t5", "11. Catat pembinaan", 1560, 360, "#166534")
    t6 = act("t6", "13. Ekspor laporan", 1760, 450, "#166534")

    k1 = act("k1", "10. Setujui atau tolak", 1320, 634, "#a16207", 190)
    k2 = act("k2", "13. Unduh laporan", 1680, 634, "#a16207")
    end_no = d.node("end-no", "Selesai", END, 1160, 648, 34, 34)

    s1 = act("s1", "4. Status verifikasi menunggu", 680, 860, "#5b21b6", 210)
    s2 = act("s2", "7. Hitung skor dan buat rekomendasi", 1020, 850, "#5b21b6", 240, 80)

    d.edge(start, p1)
    d.edge(p1, p2)
    d.edge(p2, p3)
    d.edge(p3, s1, exit_xy=(0.5, 1), entry_xy=(0.5, 0))
    d.edge(p3, s2, "selesai", exit_xy=(1, 0.5), entry_xy=(0, 0))
    d.edge(p2, t1, "atau", style=DASHED, exit_xy=(0.5, 1), entry_xy=(0.5, 0))
    d.edge(t1, s1, exit_xy=(0.5, 1), entry_xy=(0, 0.5))
    d.edge(s1, t2, exit_xy=(1, 0.5), entry_xy=(0, 0.5))
    d.edge(t2, p4, "status", exit_xy=(0.5, 0), entry_xy=(0.25, 1))
    d.edge(t3, s2, "program aktif", exit_xy=(1, 0.5), entry_xy=(0, 0.5))
    d.edge(s2, p4, exit_xy=(0.5, 0), entry_xy=(0.5, 1))
    d.edge(s2, t4, exit_xy=(1, 0.5), entry_xy=(0, 0.5))
    d.edge(t4, k1, exit_xy=(0.5, 1), entry_xy=(0.5, 0))
    d.edge(k1, t5, "Ya", exit_xy=(1, 0.25), entry_xy=(0.5, 1))
    d.edge(k1, end_no, "Tidak", exit_xy=(0, 0.5), entry_xy=(1, 0.5))
    d.edge(t5, p5, exit_xy=(0.5, 0), entry_xy=(0.5, 1))
    d.edge(p5, end)
    d.edge(t5, t6, exit_xy=(1, 0.75), entry_xy=(0, 0.5))
    d.edge(k1, k2, exit_xy=(1, 0.5), entry_xy=(0, 0.5))

    d.node(
        "note-swim",
        "Angka adalah urutan alur utama. Kotak 2b adalah jalur petugas mendata UMKM yang belum punya akun. Rekomendasi memakai pembobotan jawaban, bukan fuzzy clustering.",
        NOTE,
        200,
        1020,
        1500,
        36,
    )
    return d


def use_case():
    d = Diagram("3.2 Use Case", "skpl-32", 1680, 1240)
    d.title("Gambar 3.2 Use Case Diagram TUMBUH UMKM", w=800)
    d.node(
        "boundary",
        "TUMBUH UMKM",
        "rounded=0;whiteSpace=wrap;html=1;fillColor=#f8fafc;strokeColor=#1e3a8a;fontStyle=1;fontSize=16;verticalAlign=top;spacingTop=8;align=center;",
        250,
        60,
        1040,
        1100,
    )
    actor = "shape=umlActor;verticalLabelPosition=bottom;verticalAlign=top;html=1;outlineConnect=0;fontStyle=1;fontSize=13;fontColor=#0f172a;"
    d.node("a-owner", "Pemilik UMKM", actor, 70, 430, 40, 70)
    d.node("a-officer", "Petugas Desa", actor, 1420, 280, 40, 70)
    d.node("a-head", "Kepala Desa", actor, 1420, 820, 40, 70)
    d.node("a-system", "Sistem", actor, 70, 760, 40, 70)

    uc = "ellipse;whiteSpace=wrap;html=1;fillColor=#ffffff;strokeColor=#1e3a8a;fontSize=12;fontColor=#0f172a;align=center;arcSize=50;"
    cases = {
        "uc02": (330, 120, "UC-02 Registrasi akun pemilik UMKM"),
        "uc04": (330, 230, "UC-04 Mendata profil usaha"),
        "uc05": (330, 340, "UC-05 Mengelola produk"),
        "uc06": (330, 450, "UC-06 Mengisi kuesioner kebutuhan dan kendala"),
        "uc09": (330, 590, "UC-09 Menghitung skor dan tingkat kendala"),
        "uc11": (330, 730, "UC-11 Melihat rekomendasi program"),
        "uc15": (330, 860, "UC-15 Melihat dasbor"),
        "uc01": (820, 120, "UC-01 Login dan logout"),
        "uc03": (820, 230, "UC-03 Mengelola profil akun"),
        "uc07": (820, 340, "UC-07 Memverifikasi data UMKM"),
        "uc08": (820, 450, "UC-08 Mengelola kategori kendala dan bank soal"),
        "uc10": (820, 560, "UC-10 Mengelola program bantuan"),
        "uc12": (820, 670, "UC-12 Mengajukan tindak lanjut"),
        "uc13": (820, 780, "UC-13 Menyetujui atau menolak tindak lanjut"),
        "uc14": (820, 890, "UC-14 Mencatat dan melihat pembinaan"),
        "uc16": (820, 1000, "UC-16 Mengekspor laporan"),
    }
    for cid, (x, y, label) in cases.items():
        d.node(cid, label, uc, x, y, 280, 78)

    plain = "endArrow=none;html=1;strokeColor=#475569;endFill=0;fontSize=11;"
    owner = ["uc02", "uc04", "uc05", "uc06", "uc11", "uc15", "uc01", "uc03", "uc14"]
    officer = ["uc01", "uc03", "uc04", "uc05", "uc06", "uc07", "uc08", "uc10", "uc11", "uc12", "uc14", "uc15", "uc16"]
    head = ["uc01", "uc03", "uc11", "uc13", "uc14", "uc15", "uc16"]
    for cid in owner:
        d.edge("a-owner", cid, style=plain)
    for cid in officer:
        d.edge("a-officer", cid, style=plain)
    for cid in head:
        d.edge("a-head", cid, style=plain)
    d.edge("a-system", "uc09", style=plain)
    d.edge(
        "uc06",
        "uc09",
        "«include»",
        style="endArrow=open;endFill=0;dashed=1;html=1;strokeColor=#5b21b6;fontSize=11;",
        exit_xy=(0.5, 1),
        entry_xy=(0.5, 0),
    )
    d.node(
        "note-uc",
        "Garis tanpa panah adalah asosiasi aktor. UC-09 dijalankan sistem saat kuesioner diselesaikan.",
        NOTE,
        250,
        1170,
        900,
        30,
    )
    return d


def terminator(d, cid, text, x, y, kind="start"):
    style = START if kind == "start" else END
    return d.node(cid, text, style, x, y, 34, 34)


def activity_auth():
    d = Diagram("3.3 Activity Registrasi Login Profil", "skpl-33", 1860, 1180)
    d.title("Gambar 3.3 Activity Diagram Registrasi, Login, dan Profil Usaha", w=1100)
    groups = [
        (30, "Login (UC-01)", "#e0f2fe", "#0369a1"),
        (640, "Registrasi (UC-02)", "#dcfce7", "#166534"),
        (1250, "Profil dan produk (UC-04, UC-05)", "#fef9c3", "#a16207"),
    ]
    for x, label, fill, stroke in groups:
        d.node(d.nid("bg"), "", f"rounded=1;whiteSpace=wrap;html=1;fillColor={fill};strokeColor={stroke};arcSize=4;", x, 56, 580, 1060)
        d.node(d.nid("h"), label, f"text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=14;fontStyle=1;fontColor={stroke};", x, 64, 580, 28)

    def flow(prefix, x, steps):
        ids = {}
        for cid, kind, text, y, w, h, dx in steps:
            full = f"{prefix}-{cid}"
            if kind == "start":
                ids[cid] = terminator(d, full, text, x + dx, y, "start")
            elif kind == "end":
                ids[cid] = terminator(d, full, text, x + dx, y, "end")
            elif kind == "dec":
                ids[cid] = d.node(full, text, DEC, x + dx, y, w, h)
            elif kind == "sys":
                ids[cid] = d.node(full, text, SYSTEM, x + dx, y, w, h)
            else:
                ids[cid] = d.node(full, text, ACTION, x + dx, y, w, h)
        return ids

    # Login
    L = flow(
        "L",
        50,
        [
            ("s", "start", "Mulai", 110, 34, 34, 250),
            ("a1", "act", "Buka halaman login", 170, 220, 58, 150),
            ("a2", "act", "Masukkan email dan kata sandi", 250, 220, 58, 150),
            ("d1", "dec", "Kredensial valid?", 340, 170, 100, 175),
            ("err", "act", "Tampilkan pesan kesalahan", 470, 210, 58, 300),
            ("d2", "dec", "Akun aktif?", 500, 160, 96, 40),
            ("off", "act", "Tampilkan pesan akun nonaktif", 640, 210, 58, 20),
            ("dash", "sys", "Buka dasbor sesuai peran", 760, 220, 58, 150),
            ("e1", "end", "Selesai", 860, 34, 34, 243),
            ("e2", "end", "Selesai", 760, 34, 34, 20),
        ],
    )
    d.edge(L["s"], L["a1"], exit_xy=(0.5, 1), entry_xy=(0.5, 0))
    d.edge(L["a1"], L["a2"])
    d.edge(L["a2"], L["d1"])
    d.edge(L["d1"], L["err"], "Tidak", exit_xy=(1, 0.5), entry_xy=(0.5, 0))
    d.edge(L["err"], L["a2"], "", exit_xy=(1, 0.5), entry_xy=(1, 0.5), points=[(560, 499), (560, 279)])
    d.edge(L["d1"], L["d2"], "Ya", exit_xy=(0, 0.5), entry_xy=(0.5, 0))
    d.edge(L["d2"], L["off"], "Tidak", exit_xy=(0.5, 1), entry_xy=(0.5, 0))
    d.edge(L["off"], L["e2"])
    d.edge(L["d2"], L["dash"], "Ya", exit_xy=(1, 0.5), entry_xy=(0, 0.5))
    d.edge(L["dash"], L["e1"])

    R = flow(
        "R",
        660,
        [
            ("s", "start", "Mulai", 110, 34, 34, 250),
            ("a1", "act", "Isi formulir registrasi", 180, 230, 58, 150),
            ("d1", "dec", "Lengkap dan email unik?", 280, 190, 110, 170),
            ("err", "act", "Tampilkan pesan kesalahan", 430, 220, 58, 310),
            ("save", "sys", "Simpan akun pemilik dan kerangka usaha", 470, 240, 70, 140),
            ("go", "sys", "Arahkan ke halaman login", 580, 220, 58, 150),
            ("e", "end", "Selesai", 690, 34, 34, 243),
        ],
    )
    d.edge(R["s"], R["a1"])
    d.edge(R["a1"], R["d1"])
    d.edge(R["d1"], R["err"], "Tidak", exit_xy=(1, 0.5), entry_xy=(0.5, 0))
    d.edge(R["err"], R["a1"], "", exit_xy=(1, 0.5), entry_xy=(1, 0.5), points=[(1180, 459), (1180, 209)])
    d.edge(R["d1"], R["save"], "Ya", exit_xy=(0.5, 1), entry_xy=(0.5, 0))
    d.edge(R["save"], R["go"])
    d.edge(R["go"], R["e"])

    P = flow(
        "P",
        1270,
        [
            ("s", "start", "Mulai", 110, 34, 34, 250),
            ("a1", "act", "Buka formulir profil atau produk", 170, 240, 58, 145),
            ("a2", "act", "Isi data lalu simpan", 250, 220, 58, 155),
            ("d1", "dec", "Data valid?", 340, 160, 96, 185),
            ("err", "act", "Tampilkan pesan kesalahan", 470, 210, 58, 320),
            ("save", "sys", "Simpan data", 480, 180, 54, 50),
            ("d2", "dec", "Diisi pemilik UMKM?", 580, 180, 100, 165),
            ("pend", "sys", "Status verifikasi menjadi menunggu", 730, 230, 64, 140),
            ("e1", "end", "Selesai", 840, 34, 34, 238),
            ("e2", "end", "Selesai", 760, 34, 34, 40),
        ],
    )
    d.edge(P["s"], P["a1"])
    d.edge(P["a1"], P["a2"])
    d.edge(P["a2"], P["d1"])
    d.edge(P["d1"], P["err"], "Tidak", exit_xy=(1, 0.5), entry_xy=(0.5, 0))
    d.edge(P["err"], P["a2"], "", exit_xy=(1, 0.5), entry_xy=(1, 0.5), points=[(1810, 499), (1810, 279)])
    d.edge(P["d1"], P["save"], "Ya", exit_xy=(0, 0.5), entry_xy=(1, 0.5))
    d.edge(P["save"], P["d2"])
    d.edge(P["d2"], P["pend"], "Ya", exit_xy=(0.5, 1), entry_xy=(0.5, 0))
    d.edge(P["pend"], P["e1"])
    d.edge(P["d2"], P["e2"], "Tidak, petugas", exit_xy=(0, 0.5), entry_xy=(0.5, 0))
    return d


def activity_verify():
    d = Diagram("3.4 Activity Verifikasi", "skpl-34", 1280, 1100)
    d.title("Gambar 3.4 Activity Diagram Verifikasi Data", w=800)
    x = 80
    s = terminator(d, "s", "Mulai", 300, 70)
    a1 = d.node("a1", "Buka daftar data menunggu", ACTION, 200, 140, 240, 58)
    d1 = d.node("d1", "Ada data menunggu?", DEC, 230, 230, 180, 100)
    empty = d.node("empty", "Tidak ada antrean", ACTION, 520, 250, 180, 58)
    e0 = terminator(d, "e0", "Selesai", 590, 350)
    a2 = d.node("a2", "Periksa kelengkapan dan kewajaran data", ACTION, 180, 370, 280, 64)
    d2 = d.node("d2", "Terverifikasi?", DEC, 240, 470, 160, 96)
    ok = d.node("ok", "Simpan status terverifikasi", SYSTEM, 40, 620, 210, 58)
    d3 = d.node("d3", "Perlu perbaikan?", DEC, 470, 610, 170, 96)
    fix = d.node("fix", "Isi catatan, simpan perlu perbaikan", SYSTEM, 280, 760, 240, 64)
    rej = d.node("rej", "Isi catatan, simpan ditolak", SYSTEM, 620, 760, 220, 64)
    show = d.node("show", "Status tampil pada akun pemilik", SYSTEM, 160, 880, 250, 58)
    e1 = terminator(d, "e1", "Selesai", 268, 980)
    d.edge(s, a1)
    d.edge(a1, d1)
    d.edge(d1, empty, "Tidak", exit_xy=(1, 0.5), entry_xy=(0, 0.5))
    d.edge(empty, e0)
    d.edge(d1, a2, "Ya")
    d.edge(a2, d2)
    d.edge(d2, ok, "Ya", exit_xy=(0, 0.5), entry_xy=(0.5, 0))
    d.edge(d2, d3, "Tidak", exit_xy=(1, 0.5), entry_xy=(0.5, 0))
    d.edge(d3, fix, "Ya", exit_xy=(0, 0.5), entry_xy=(1, 0))
    d.edge(d3, rej, "Tidak", exit_xy=(1, 0.5), entry_xy=(0.5, 0))
    d.edge(ok, show)
    d.edge(fix, show)
    d.edge(rej, show, exit_xy=(0.5, 1), entry_xy=(1, 0.5))
    d.edge(show, e1)
    d.node(
        "note",
        "Jika pemilik memperbaiki data, status kembali menjadi menunggu dan masuk antrean verifikasi lagi.",
        NOTE,
        520,
        900,
        420,
        40,
    )
    return d


def activity_score():
    d = Diagram("3.5 Activity Kuesioner dan Pembobotan", "skpl-35", 1280, 1180)
    d.title("Gambar 3.5 Activity Diagram Kuesioner dan Pembobotan Skor", w=980)
    s = terminator(d, "s", "Mulai", 300, 60)
    a1 = d.node("a1", "Buka kuesioner kebutuhan dan kendala", ACTION, 190, 130, 260, 58)
    a2 = d.node("a2", "Jawab pertanyaan per kategori", ACTION, 190, 220, 260, 58)
    d1 = d.node("d1", "Simpan sebagai?", DEC, 230, 310, 180, 100)
    draft = d.node("draft", "Simpan draf", SYSTEM, 520, 330, 170, 54)
    e0 = terminator(d, "e0", "Selesai", 588, 430)
    d2 = d.node("d2", "Semua soal terjawab?", DEC, 220, 460, 200, 110)
    miss = d.node("miss", "Minta soal wajib dilengkapi", ACTION, 560, 480, 220, 58)
    calc = d.node("calc", "Hitung skor. Jika soal terbalik, pakai 100 dikurangi skor pilihan", SYSTEM, 150, 620, 340, 70)
    level = d.node("level", "Bandingkan skor dengan ambang sedang dan tinggi", SYSTEM, 160, 720, 320, 64)
    save = d.node("save", "Simpan tingkat rendah, sedang, atau tinggi", SYSTEM, 160, 820, 320, 58)
    primary = d.node("primary", "Kendala utama = kategori berskor tertinggi", SYSTEM, 160, 910, 320, 58)
    e1 = terminator(d, "e1", "Selesai", 303, 1010)
    d.edge(s, a1)
    d.edge(a1, a2)
    d.edge(a2, d1)
    d.edge(d1, draft, "Draf", exit_xy=(1, 0.5), entry_xy=(0, 0.5))
    d.edge(draft, e0)
    d.edge(d1, d2, "Selesai")
    d.edge(d2, miss, "Tidak", exit_xy=(1, 0.5), entry_xy=(0, 0.5))
    d.edge(miss, a2, "", exit_xy=(0.5, 0), entry_xy=(1, 0.5), points=[(670, 200)])
    d.edge(d2, calc, "Ya")
    d.edge(calc, level)
    d.edge(level, save)
    d.edge(save, primary)
    d.edge(primary, e1)
    d.node(
        "formula",
        "Skor kategori = jumlah(skor pilihan × bobot) / jumlah(100 × bobot) × 100. Ambang bawaan: sedang 40, tinggi 70.",
        NOTE,
        560,
        760,
        420,
        48,
    )
    return d


def activity_reco():
    d = Diagram("3.6 Activity Rekomendasi dan Tindak Lanjut", "skpl-36", 1500, 1240)
    d.title("Gambar 3.6 Activity Diagram Rekomendasi, Pengajuan, dan Persetujuan", w=1100)
    s = terminator(d, "s", "Mulai", 250, 60)
    d1 = d.node("d1", "Program aktif sudah ada?", DEC, 160, 130, 210, 110)
    make = d.node("make", "Petugas mengisi program, menautkan kategori dan tingkat minimum", ACTION, 460, 145, 280, 80)
    match = d.node("match", "Bandingkan tingkat kendala dengan tingkat minimum program", SYSTEM, 130, 280, 280, 70)
    d2 = d.node("d2", "Ada program yang cocok?", DEC, 170, 390, 200, 110)
    e0 = terminator(d, "e0", "Selesai", 520, 428)
    save = d.node("save", "Simpan rekomendasi. Nonaktifkan rekomendasi kuesioner lama", SYSTEM, 140, 540, 270, 70)
    show = d.node("show", "Tampilkan rekomendasi pada petugas, kepala desa, dan pemilik", SYSTEM, 120, 650, 310, 70)
    d3 = d.node("d3", "Petugas mengajukan tindak lanjut?", DEC, 150, 760, 240, 110)
    e1 = terminator(d, "e1", "Selesai", 520, 798)
    plan = d.node("plan", "Isi rencana kegiatan lalu ajukan", ACTION, 160, 910, 230, 58)
    review = d.node("review", "Kepala desa membuka antrean", ACTION, 700, 910, 230, 58)
    d4 = d.node("d4", "Disetujui?", DEC, 740, 1010, 150, 90)
    yes = d.node("yes", "Simpan status disetujui", SYSTEM, 980, 960, 200, 54)
    no = d.node("no", "Isi catatan, simpan status ditolak", SYSTEM, 960, 1060, 230, 58)
    e2 = terminator(d, "e2", "Selesai", 1063, 1160)
    d.edge(s, d1)
    d.edge(d1, make, "Tidak", exit_xy=(1, 0.5), entry_xy=(0, 0.5))
    d.edge(make, match, "", exit_xy=(0.5, 1), entry_xy=(1, 0.3))
    d.edge(d1, match, "Ya")
    d.edge(match, d2)
    d.edge(d2, e0, "Tidak", exit_xy=(1, 0.5), entry_xy=(0, 0.5))
    d.edge(d2, save, "Ya")
    d.edge(save, show)
    d.edge(show, d3)
    d.edge(d3, e1, "Tidak", exit_xy=(1, 0.5), entry_xy=(0, 0.5))
    d.edge(d3, plan, "Ya")
    d.edge(plan, review, exit_xy=(1, 0.5), entry_xy=(0, 0.5))
    d.edge(review, d4)
    d.edge(d4, yes, "Ya", exit_xy=(1, 0.35), entry_xy=(0, 0.5))
    d.edge(d4, no, "Tidak", exit_xy=(1, 0.7), entry_xy=(0, 0.5))
    d.edge(yes, e2, exit_xy=(0.5, 1), entry_xy=(0.5, 0))
    d.edge(no, e2)
    d.node(
        "note",
        "Tingkat minimum sedang mencakup sedang dan tinggi. Tingkat minimum tinggi hanya mencakup tinggi. Tingkat rendah tidak direkomendasikan.",
        NOTE,
        700,
        300,
        520,
        48,
    )
    return d


def activity_report():
    d = Diagram("3.7 Activity Pembinaan dan Laporan", "skpl-37", 1500, 980)
    d.title("Gambar 3.7 Activity Diagram Pembinaan dan Ekspor Laporan", w=980)
    d.node("bg1", "", "rounded=1;fillColor=#dcfce7;strokeColor=#166534;html=1;", 30, 56, 680, 880)
    d.node("bg2", "", "rounded=1;fillColor=#fef9c3;strokeColor=#a16207;html=1;", 740, 56, 720, 880)
    d.node("h1", "Mencatat pembinaan (UC-14)", "text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=14;fontStyle=1;fontColor=#166534;", 30, 64, 680, 28)
    d.node("h2", "Laporan dan ekspor (UC-16)", "text;html=1;strokeColor=none;fillColor=none;align=center;fontSize=14;fontStyle=1;fontColor=#a16207;", 740, 64, 720, 28)

    s1 = terminator(d, "s1", "Mulai", 330, 110)
    a1 = d.node("a1", "Buka tindak lanjut yang dipilih", ACTION, 230, 180, 240, 58)
    d1 = d.node("d1", "Statusnya disetujui?", DEC, 250, 270, 200, 100)
    deny = d.node("deny", "Tolak pencatatan pembinaan", SYSTEM, 500, 290, 180, 58)
    e_deny = terminator(d, "e-deny", "Selesai", 573, 390)
    form = d.node("form", "Isi judul, tanggal, uraian, dan hasil", ACTION, 220, 420, 260, 64)
    save = d.node("save", "Simpan sesi pembinaan", SYSTEM, 240, 520, 220, 54)
    e1 = terminator(d, "e1", "Selesai", 333, 620)
    d.edge(s1, a1)
    d.edge(a1, d1)
    d.edge(d1, deny, "Tidak", exit_xy=(1, 0.5), entry_xy=(0, 0.5))
    d.edge(deny, e_deny)
    d.edge(d1, form, "Ya")
    d.edge(form, save)
    d.edge(save, e1)

    s2 = terminator(d, "s2", "Mulai", 1060, 110)
    a2 = d.node("a2", "Pilih filter periode atau kategori", ACTION, 960, 180, 250, 58)
    view = d.node("view", "Tampilkan ringkasan laporan", SYSTEM, 970, 270, 230, 58)
    d2 = d.node("d2", "Minta unduhan?", DEC, 990, 360, 180, 96)
    e2 = terminator(d, "e2", "Selesai", 820, 500)
    d3 = d.node("d3", "Format berkas?", DEC, 1000, 500, 160, 90)
    pdf = d.node("pdf", "Buat berkas PDF", SYSTEM, 820, 640, 170, 54)
    xls = d.node("xls", "Buat berkas Excel", SYSTEM, 1060, 640, 180, 54)
    give = d.node("give", "Sediakan berkas untuk diunduh", SYSTEM, 930, 740, 240, 54)
    e3 = terminator(d, "e3", "Selesai", 1033, 840)
    d.edge(s2, a2)
    d.edge(a2, view)
    d.edge(view, d2)
    d.edge(d2, e2, "Tidak", exit_xy=(0, 0.5), entry_xy=(1, 0.5))
    d.edge(d2, d3, "Ya")
    d.edge(d3, pdf, "PDF", exit_xy=(0, 0.5), entry_xy=(0.5, 0))
    d.edge(d3, xls, "Excel", exit_xy=(1, 0.5), entry_xy=(0.5, 0))
    d.edge(pdf, give)
    d.edge(xls, give)
    d.edge(give, e3)
    return d


def html_entity(name, rows):
    lines = "".join(rows)
    return f"<b>{name}</b><hr>{lines}"


def erd():
    d = Diagram("3.8 ERD", "skpl-38", 2480, 1960)
    d.title("Gambar 3.8 ERD TUMBUH UMKM", w=700)
    d.node(
        "legend",
        "Notasi crow's foot. Satu di sisi kiri relasi berarti tepat satu atau nol-satu. Kaki gagak berarti banyak. Tidak ada tabel peran dan tidak ada tabel dokumen legalitas.",
        NOTE,
        520,
        16,
        1400,
        28,
    )

    def attr(text, kind=""):
        if kind == "pk":
            return f"<u>{text}</u> PK<br>"
        if kind == "fk":
            return f"<i>{text}</i> FK<br>"
        return f"{text}<br>"

    entities = {
        "users": (40, 70, ["id", "name", "email", "password", "role", "phone", "is_active"], {"id": "pk"}),
        "business_types": (40, 280, ["id", "name", "slug", "is_active"], {"id": "pk"}),
        "businesses": (
            420,
            70,
            [
                "id",
                "user_id",
                "business_type_id",
                "created_by",
                "business_name",
                "owner_name",
                "phone",
                "email",
                "address",
                "hamlet",
                "rt",
                "rw",
                "established_year",
                "employee_count",
                "description",
                "operational_status",
                "verification_status",
                "verification_note",
                "verified_by",
                "verified_at",
            ],
            {"id": "pk", "user_id": "fk", "business_type_id": "fk", "created_by": "fk", "verified_by": "fk"},
        ),
        "products": (
            860,
            70,
            ["id", "business_id", "name", "category", "description", "price", "unit", "photo_path", "is_active"],
            {"id": "pk", "business_id": "fk"},
        ),
        "obstacle_categories": (
            1220,
            70,
            ["id", "name", "slug", "description", "moderate_threshold", "high_threshold", "sort_order", "is_active"],
            {"id": "pk"},
        ),
        "assessment_questions": (
            1600,
            70,
            ["id", "obstacle_category_id", "type", "prompt", "help_text", "weight", "is_reverse_scored", "sort_order", "is_active"],
            {"id": "pk", "obstacle_category_id": "fk"},
        ),
        "question_options": (
            2020,
            70,
            ["id", "assessment_question_id", "label", "value", "score", "sort_order"],
            {"id": "pk", "assessment_question_id": "fk"},
        ),
        "assessments": (
            1220,
            340,
            ["id", "business_id", "filled_by", "status", "is_current", "primary_obstacle_category_id", "other_obstacle", "completed_at"],
            {"id": "pk", "business_id": "fk", "filled_by": "fk", "primary_obstacle_category_id": "fk"},
        ),
        "assessment_answers": (
            1640,
            360,
            ["id", "assessment_id", "assessment_question_id", "question_option_id", "score", "weight"],
            {"id": "pk", "assessment_id": "fk", "assessment_question_id": "fk", "question_option_id": "fk"},
        ),
        "assessment_category_scores": (
            2020,
            340,
            ["id", "assessment_id", "obstacle_category_id", "score", "level"],
            {"id": "pk", "assessment_id": "fk", "obstacle_category_id": "fk"},
        ),
        "assistance_programs": (
            40,
            760,
            ["id", "name", "description", "provider", "requirements", "url", "quota", "starts_on", "ends_on", "status", "created_by"],
            {"id": "pk", "created_by": "fk"},
        ),
        "program_category": (
            460,
            820,
            ["id", "assistance_program_id", "obstacle_category_id", "minimum_level"],
            {"id": "pk", "assistance_program_id": "fk", "obstacle_category_id": "fk"},
        ),
        "program_recommendations": (
            860,
            780,
            ["id", "business_id", "assistance_program_id", "assessment_id", "obstacle_category_id", "score", "is_active"],
            {"id": "pk", "business_id": "fk", "assistance_program_id": "fk", "assessment_id": "fk", "obstacle_category_id": "fk"},
        ),
        "follow_ups": (
            1280,
            760,
            ["id", "business_id", "program_recommendation_id", "assistance_program_id", "submitted_by", "planned_activity", "planned_on", "submission_note", "status", "decided_by", "decision_note", "decided_at"],
            {"id": "pk", "business_id": "fk", "program_recommendation_id": "fk", "assistance_program_id": "fk", "submitted_by": "fk", "decided_by": "fk"},
        ),
        "coaching_sessions": (
            1760,
            800,
            ["id", "follow_up_id", "business_id", "assistance_program_id", "recorded_by", "title", "held_on", "description", "outcome", "status"],
            {"id": "pk", "follow_up_id": "fk", "business_id": "fk", "assistance_program_id": "fk", "recorded_by": "fk"},
        ),
    }

    style = "rounded=0;whiteSpace=wrap;html=1;align=left;verticalAlign=top;spacingLeft=8;spacingRight=6;spacingTop=6;fillColor=#ffffff;strokeColor=#1e3a8a;fontSize=11;fontColor=#0f172a;"
    heights = {}
    for name, (x, y, fields, kinds) in entities.items():
        rows = "".join(attr(field, kinds.get(field, "")) for field in fields)
        h = 36 + 16 * len(fields)
        heights[name] = h
        d.node(name, html_entity(name, rows), style, x, y, 250, h)

    def rel(src, tgt, start_arrow, end_arrow, label, exit_xy, entry_xy):
        style_rel = (
            "edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;strokeColor=#0f172a;fontSize=11;"
            f"startArrow={start_arrow};startFill=0;endArrow={end_arrow};endFill=0;"
        )
        d.edge(src, tgt, label, style=style_rel, exit_xy=exit_xy, entry_xy=entry_xy)

    one = "ERmandOne"
    zone = "ERzeroToOne"
    many = "ERzeroToMany"
    rel("users", "businesses", zone, zone, "pemilik", (1, 0.4), (0, 0.12))
    rel("business_types", "businesses", one, many, "", (1, 0.5), (0, 0.72))
    rel("businesses", "products", one, many, "", (1, 0.18), (0, 0.5))
    rel("businesses", "assessments", one, many, "", (1, 0.9), (0, 0.5))
    rel("obstacle_categories", "assessment_questions", one, many, "", (1, 0.35), (0, 0.4))
    rel("assessment_questions", "question_options", one, many, "", (1, 0.4), (0, 0.5))
    rel("assessments", "assessment_answers", one, many, "", (1, 0.35), (0, 0.4))
    rel("assessment_questions", "assessment_answers", one, many, "", (0.5, 1), (0.5, 0))
    rel("question_options", "assessment_answers", one, many, "", (0.5, 1), (1, 0.3))
    rel("assessments", "assessment_category_scores", one, many, "", (1, 0.7), (0, 0.45))
    rel("obstacle_categories", "assessment_category_scores", one, many, "", (1, 0.85), (0, 0.2))
    rel("obstacle_categories", "assessments", many, zone, "kendala utama", (0.3, 1), (0.5, 0))
    rel("assistance_programs", "program_category", one, many, "", (1, 0.4), (0, 0.5))
    rel("obstacle_categories", "program_category", one, many, "", (0, 1), (0.5, 0))
    rel("businesses", "program_recommendations", one, many, "", (0.5, 1), (0.5, 0))
    rel("assistance_programs", "program_recommendations", one, many, "", (1, 0.75), (0, 0.4))
    rel("assessments", "program_recommendations", one, many, "", (0.2, 1), (1, 0.2))
    rel("obstacle_categories", "program_recommendations", one, many, "alasan", (0.7, 1), (0.7, 0))
    rel("businesses", "follow_ups", one, many, "", (1, 0.55), (0, 0.15))
    rel("program_recommendations", "follow_ups", zone, many, "", (1, 0.6), (0, 0.4))
    rel("assistance_programs", "follow_ups", zone, many, "", (1, 1), (0, 0.85))
    rel("follow_ups", "coaching_sessions", one, zone, "", (1, 0.45), (0, 0.4))
    rel("businesses", "coaching_sessions", one, many, "", (1, 1), (0, 0.85))
    return d


def class_box(name, attrs, methods):
    attr_html = "<br>".join(f"+ {item}" for item in attrs)
    method_html = "<br>".join(f"+ {item}" for item in methods)
    return f"<p style='margin:0;text-align:center'><b>{name}</b></p><hr>{attr_html}<hr>{method_html}"


def class_diagram():
    d = Diagram("3.9 Class Diagram", "skpl-39", 2100, 1500)
    d.title("Gambar 3.9 Class Diagram TUMBUH UMKM", w=800)
    style = "rounded=0;whiteSpace=wrap;html=1;align=left;verticalAlign=top;spacingLeft=8;spacingRight=6;spacingTop=4;fillColor=#ffffff;strokeColor=#0f172a;fontSize=11;fontColor=#0f172a;"
    classes = {
        "User": (40, 60, ["name", "email", "role", "is_active"], ["login()", "logout()", "register()", "updateProfil()", "hasRole()"]),
        "BusinessType": (320, 60, ["name", "slug", "is_active"], ["daftarAktif()"]),
        "Business": (600, 60, ["business_name", "verification_status", "operational_status"], ["simpan()", "perbarui()", "verifikasi()", "mintaPerbaikan()", "tolak()"]),
        "Product": (920, 60, ["name", "category", "price", "is_active"], ["tambah()", "ubah()", "nonaktifkan()"]),
        "ObstacleCategory": (40, 380, ["name", "moderate_threshold", "high_threshold"], ["tambah()", "ubah()", "tentukanTingkat()"]),
        "AssessmentQuestion": (340, 380, ["prompt", "weight", "is_reverse_scored"], ["tambah()", "ubah()", "nonaktifkan()"]),
        "QuestionOption": (680, 380, ["label", "value", "score"], ["tambah()", "ubah()"]),
        "Assessment": (980, 380, ["status", "is_current", "other_obstacle"], ["simpanDraf()", "selesaikan()", "tandaiBerjalan()"]),
        "AssessmentAnswer": (40, 700, ["score", "weight"], ["simpan()"]),
        "CategoryScore": (320, 700, ["score", "level"], ["hitung()", "simpan()"]),
        "AssistanceProgram": (600, 700, ["name", "provider", "status"], ["tambah()", "ubah()", "nonaktifkan()"]),
        "Recommendation": (920, 700, ["score", "is_active"], ["generate()", "nonaktifkan()"]),
        "FollowUp": (320, 980, ["planned_activity", "status", "decision_note"], ["ajukan()", "setujui()", "tolak()"]),
        "CoachingSession": (680, 980, ["title", "held_on", "outcome", "status"], ["catat()", "ubahStatus()"]),
        "Report": (1040, 980, ["filter periode", "filter kategori"], ["ringkasan()", "eksporPdf()", "eksporExcel()"]),
    }
    for name, (x, y, attrs, methods) in classes.items():
        h = 78 + 16 * (len(attrs) + len(methods))
        d.node(name, class_box(name, attrs, methods), style, x, y, 250, h)

    def assoc(src, tgt, left, right, exit_xy, entry_xy):
        style_a = "edgeStyle=orthogonalEdgeStyle;rounded=0;html=1;endArrow=none;endFill=0;strokeColor=#0f172a;fontSize=11;"
        d.edge(src, tgt, f"{left}    {right}", style=style_a, exit_xy=exit_xy, entry_xy=entry_xy)

    assoc("User", "Business", "1", "0..1", (1, 0.4), (0, 0.3))
    assoc("BusinessType", "Business", "1", "*", (1, 0.5), (0, 0.55))
    assoc("Business", "Product", "1", "*", (1, 0.35), (0, 0.4))
    assoc("Business", "Assessment", "1", "*", (0.7, 1), (0.5, 0))
    assoc("ObstacleCategory", "AssessmentQuestion", "1", "*", (1, 0.4), (0, 0.4))
    assoc("AssessmentQuestion", "QuestionOption", "1", "*", (1, 0.4), (0, 0.4))
    assoc("Assessment", "AssessmentAnswer", "1", "*", (0, 1), (1, 0.3))
    assoc("AssessmentQuestion", "AssessmentAnswer", "1", "*", (0.3, 1), (0.7, 0))
    assoc("QuestionOption", "AssessmentAnswer", "1", "*", (0.2, 1), (1, 0.6))
    assoc("Assessment", "CategoryScore", "1", "*", (0.3, 1), (1, 0.2))
    assoc("ObstacleCategory", "CategoryScore", "1", "*", (0.5, 1), (0.5, 0))
    assoc("Assessment", "ObstacleCategory", "*", "0..1", (0, 0.4), (1, 0.7))
    assoc("ObstacleCategory", "AssistanceProgram", "*", "*", (0.8, 1), (0, 0.2))
    assoc("Business", "Recommendation", "1", "*", (1, 0.8), (0.5, 0))
    assoc("AssistanceProgram", "Recommendation", "1", "*", (1, 0.5), (0, 0.5))
    assoc("Assessment", "Recommendation", "1", "*", (1, 0.7), (0, 0.2))
    assoc("Business", "FollowUp", "1", "*", (0.4, 1), (0.5, 0))
    assoc("Recommendation", "FollowUp", "1", "*", (0.3, 1), (1, 0.3))
    assoc("FollowUp", "CoachingSession", "1", "0..1", (1, 0.5), (0, 0.4))
    assoc("Business", "CoachingSession", "1", "*", (0.85, 1), (0.5, 0))
    d.edge(
        "Report",
        "Recommendation",
        "«use»",
        style="endArrow=open;endFill=0;dashed=1;html=1;strokeColor=#5b21b6;",
        exit_xy=(0.5, 0),
        entry_xy=(0.8, 1),
    )
    d.node(
        "note",
        "Report tidak menyimpan tabel. Kelas ini membaca Business, CategoryScore, Recommendation, FollowUp, dan CoachingSession. Angka pada garis adalah kardinalitas.",
        NOTE,
        1040,
        1280,
        620,
        48,
    )
    return d


def overlaps(diagram):
    items = list(diagram.boxes.items())
    found = []
    for i, (a, (ax, ay, aw, ah)) in enumerate(items):
        if a.startswith(("title", "note", "legend", "bg", "h", "lane", "boundary", "formula")):
            continue
        for b, (bx, by, bw, bh) in items[i + 1 :]:
            if b.startswith(("title", "note", "legend", "bg", "h", "lane", "boundary", "formula")):
                continue
            if ax < bx + bw and ax + aw > bx and ay < by + bh and ay + ah > by:
                found.append(f"{a} x {b}")
    return found


def main():
    diagrams = [
        swimlane(),
        use_case(),
        activity_auth(),
        activity_verify(),
        activity_score(),
        activity_reco(),
        activity_report(),
        erd(),
        class_diagram(),
    ]
    for diagram in diagrams:
        hits = overlaps(diagram)
        if hits:
            print(f"OVERLAP {diagram.name}: {hits[:12]}")
    text = DRAWIO.read_text(encoding="utf-8")
    old = '<mxfile host="app.diagrams.net" pages="4">'
    if old not in text:
        raise SystemExit("Pembuka mxfile tidak ditemukan atau jumlah halaman sudah berubah.")
    block = "\n".join(diagram.xml() for diagram in diagrams)
    updated = text.replace(old, '<mxfile host="app.diagrams.net" pages="13">\n' + block, 1)
    DRAWIO.write_text(updated, encoding="utf-8")

    root = ET.parse(DRAWIO).getroot()
    pages = root.findall("diagram")
    print(f"halaman: {len(pages)}")
    for page in pages:
        model = page.find("mxGraphModel")
        cells = list(model.find("root"))
        ids = [cell.get("id") for cell in cells]
        if len(ids) != len(set(ids)):
            dupes = [i for i in ids if ids.count(i) > 1]
            raise SystemExit(f"ID ganda di {page.get('name')}: {set(dupes)}")
        known = set(ids)
        for cell in cells:
            for attr in ("source", "target", "parent"):
                ref = cell.get(attr)
                if ref and ref not in known and ref != "0":
                    raise SystemExit(f"Referensi {attr}={ref} tidak ada di {page.get('name')}")
        print(f"  {page.get('name')}: {len(cells)} cell")


if __name__ == "__main__":
    main()

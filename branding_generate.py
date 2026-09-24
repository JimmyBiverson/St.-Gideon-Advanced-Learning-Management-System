from PIL import Image, ImageDraw, ImageFont
import os

out_dir = os.path.join(os.getcwd(), 'branding-assets')
os.makedirs(out_dir, exist_ok=True)


def add_star(draw, cx, cy, r1, r2, num_points, color):
    points = []
    for i in range(num_points * 2):
        angle = -3.14159265 / 2 + i * 3.14159265 / num_points
        radius = r1 if i % 2 == 0 else r2
        x = cx + radius * __import__('math').cos(angle)
        y = cy + radius * __import__('math').sin(angle)
        points.append((x, y))
    draw.polygon(points, fill=color)


def create_logo(path, width=1200, height=1200, title='ST GIDEON', subtitle='JUNIOR SCHOOL', ribbon='WHERE THERE IS A WILL THERE IS A WAY'):
    img = Image.new('RGBA', (width, height), (255, 255, 255, 0))
    draw = ImageDraw.Draw(img)

    shield = [(100, 95), (1100, 95), (1200, 260), (1180, 1020), (600, 1200), (20, 1020), (0, 260)]
    draw.polygon(shield, fill=(255, 255, 255, 255), outline=(0, 116, 255, 255), width=35)

    inner = [(150, 180), (1050, 180), (1100, 300), (1080, 950), (600, 1100), (100, 950), (80, 300)]
    draw.polygon(inner, fill=(255, 255, 255, 255), outline=(0, 116, 255, 255), width=18)
    draw.arc((120, 90, 1080, 520), start=200, end=340, fill=(0, 116, 255, 255), width=40)

    try:
        font_big = ImageFont.truetype('arialbd.ttf', int(width * 0.17))
        font_med = ImageFont.truetype('arialbd.ttf', int(width * 0.10))
        font_ribbon = ImageFont.truetype('arialbd.ttf', int(width * 0.07))
    except Exception:
        font_big = ImageFont.load_default()
        font_med = ImageFont.load_default()
        font_ribbon = ImageFont.load_default()

    draw.text((width / 2, 220), title, font=font_big, fill=(0, 0, 0, 255), anchor='mm')

    cap = [(520, 390), (680, 390), (760, 520), (600, 640), (440, 520)]
    draw.polygon(cap, fill=(0, 0, 0, 255))
    draw.rectangle((540, 435, 660, 520), fill=(0, 0, 0, 255))
    draw.rectangle((570, 500, 630, 640), fill=(0, 0, 0, 255))
    draw.line((600, 390, 600, 315), fill=(255, 0, 0, 255), width=14)

    draw.rounded_rectangle((350, 540, 850, 810), radius=25, outline=(0, 0, 0, 255), width=10, fill=(255, 255, 255, 255))
    draw.line((420, 600, 760, 600), fill=(0, 0, 0, 255), width=8)
    draw.line((420, 660, 760, 660), fill=(0, 0, 0, 255), width=8)
    draw.line((420, 720, 760, 720), fill=(0, 0, 0, 255), width=8)
    draw.rounded_rectangle((462, 550, 780, 790), radius=18, outline=(0, 0, 0, 255), width=6, fill=(255, 255, 255, 255))

    add_star(draw, 120, 470, 42, 120, 10, (0, 0, 0, 255))
    add_star(draw, 1080, 470, 42, 120, 10, (0, 0, 0, 255))

    draw.rounded_rectangle((180, 860, 1020, 1030), radius=120, fill=(0, 0, 0, 255))
    draw.text((600, 940), ribbon, font=font_ribbon, fill=(255, 255, 255, 255), anchor='mm')
    draw.text((600, 835), subtitle, font=font_med, fill=(0, 0, 0, 255), anchor='mm')

    img.save(path)


create_logo(os.path.join(out_dir, 'logo.png'))
create_logo(os.path.join(out_dir, 'favicon.png'), width=512, height=512)

banner = Image.new('RGBA', (1200, 450), (255, 255, 255, 0))
bd = ImageDraw.Draw(banner)
bd.rounded_rectangle((0, 0, 1200, 450), radius=30, fill=(255, 255, 255, 255), outline=(0, 116, 255, 255), width=10)

try:
    font = ImageFont.truetype('arialbd.ttf', 120)
except Exception:
    font = ImageFont.load_default()

bd.text((600, 200), 'ST GIDEON', font=font, fill=(0, 0, 0, 255), anchor='mm')
banner.save(os.path.join(out_dir, 'banner.png'))

auth = Image.new('RGBA', (1200, 400), (255, 255, 255, 0))
ad = ImageDraw.Draw(auth)
ad.rounded_rectangle((0, 0, 1200, 400), radius=30, fill=(255, 255, 255, 255), outline=(0, 116, 255, 255), width=8)
try:
    font2 = ImageFont.truetype('arialbd.ttf', 90)
except Exception:
    font2 = ImageFont.load_default()
ad.text((600, 200), 'JUNIOR SCHOOL', font=font2, fill=(0, 0, 0, 255), anchor='mm')
auth.save(os.path.join(out_dir, 'auth-banner.png'))

print('Created:', out_dir)

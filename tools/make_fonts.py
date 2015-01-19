#!/usr/bin/env python

# convert artwiz open source fonts to javascript file
# as an array using integer arrays in place of binary

import os

SEGMENTS_ALL = 2

class BDFProcStatus:
    DEFAULT = 0
    FONT = 1
    PROPERTIES = 2
    CHAR = 3
    CHARDAT = 4
    EOF = 5
    
class BDFGlyph:
    def __init__(self):
        self.data = []
        self.x = 8
        self.y = None
        self.width = None
        self.height = None
        self.bbox = None
        self.encoding = None
        
    def push_row(self, row):
        self.data.append(bin(int(row, 16))[2:].zfill(8))
        
    def end(self):
        self.y = len(self.data)
        self.height = self.y
        return self

class BDFFont:
    def __init__(self, path):
        self.glyphs = []
        self.chars = 0
        self.font_name = None
        self.y = None
        f = open(path, 'r').readlines();
        state = BDFProcStatus.DEFAULT
        segments_visited = 0
        chars_visited = 0
        glyph = None
        for l in f:
            #print l
            args = l.strip().split(' ')
            op = args[0].strip()
            if len(args) > 1: args = args[1:]
            else: args = None
            if state == BDFProcStatus.DEFAULT:
                if op == 'STARTFONT':
                    state = BDFProcStatus.FONT 
                       
            elif state == BDFProcStatus.FONT:
                if op == 'STARTPROPERTIES':
                    state = BDFProcStatus.PROPERTIES
                elif op == 'STARTCHAR':
                    state = BDFProcStatus.CHAR
                    glyph = BDFGlyph()
                elif op == 'ENDFONT':
                    state = BDFProcStatus.EOF
                    segments_visited += 1
                elif op == 'CHARS':
                    if args:
                        self.chars = int(args[0])
                    
            elif state == BDFProcStatus.PROPERTIES:
                if op == 'ENDPROPERTIES':
                    state = BDFProcStatus.FONT
                    segments_visited += 1
                elif op == 'FAMILY_NAME':
                    self.font_name = ''.join(args[0]).replace('\"', '')
                    
            elif state == BDFProcStatus.CHAR:
                if op == 'BITMAP':
                    state = BDFProcStatus.CHARDAT
                elif op == 'ENCODING':
                    glyph.encoding = int(args[0])
                elif op == 'DWIDTH':
                    glyph.width = (int(args[0]), int(args[1]))
                    print glyph.width
                elif op == 'BBX':
                    glyph.bbox = (int(args[0]), int(args[1]), int(args[2]), int(args[3]))
                    
            elif state == BDFProcStatus.CHARDAT:
                if op == 'ENDCHAR':
                    state = BDFProcStatus.FONT
                    chars_visited += 1
                    self.glyphs.append(glyph.end())
                    self.y = glyph.y
                    glyph = None
                else:
                    glyph.push_row(op);
                    
        if segments_visited < SEGMENTS_ALL or chars_visited != self.chars:
            raise IOError('Invalid BDF input file: %s' % path)

def main():
    fonts = []
    font_path = './artwiz-1.3'
    if os.path.exists(font_path):
        files = []
        for _f in os.listdir(font_path):
            f = os.path.join(font_path, _f)
            if os.path.isfile(f):
                if f[-4:] == '.bdf': fonts.append(f)
    js_fonts = []
    js_fonts_d = {}
    for font in fonts:
        f = BDFFont(font)
        if f.font_name.upper() in ['AQUI', 'FKP', 'KATES', 'SMOOTHANSI', 'GLISPBOLD', 'SNAP']: continue
        js_fonts.append(f.font_name)
        js_fonts_d[f.font_name] = {}
        for g in f.glyphs:
            if not chr(g.encoding) in 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!': continue
            js_fonts_d[f.font_name][chr(g.encoding)] = []
            for data in g.data:
                gx = 0
                gxdata = []
                for p in data:
                    if p == '1': gxdata.append(1)
                    else: gxdata.append(0)
                    if gx >= g.width[0]:
                        break
                    gx += 1
                js_fonts_d[f.font_name][chr(g.encoding)].append(gxdata)
    # output fonts
    s = '// data derived from artwiz improved fonts: http://artwizaleczapka.sourceforge.net/\nvar fonts = [\n'
    for f in js_fonts:
        s += '\t\'' + f + '\',\n'
    s += '];\n\nvar font_data = {\n'
    for f in js_fonts_d.keys():
        s += '\t' + f + ': ' + '{\n'
        for char in js_fonts_d[f].keys():
            s += '\t\t\'' + char + '\': [\n'
            for col in js_fonts_d[f][char]:
                s += '\t\t\t['
                for n in col:
                    s += str(n) + ', '
                s += '],\n'
            s += '\t\t],\n'
        s += '\t},\n'
    s += '};\n'
    f = open('../fonts.js', 'w')
    f.writelines(s)

main()
